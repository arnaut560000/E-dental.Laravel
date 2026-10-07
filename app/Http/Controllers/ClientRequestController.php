<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ClientRequest;
use App\Services\ClinicSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ClientRequestController extends Controller
{
    public function create(ClinicSchedule $schedule)
    {
        return view('requests.create', [
            'clinicName' => config('clinic.name'),
            'barangays' => config('clinic.barangays'),
            'categories' => config('clinic.categories'),
            'genders' => config('clinic.genders'),
            'idRequired' => config('clinic.id_required_categories'),
            'scheduleSummary' => $schedule->daysLabel().' · '.$schedule->dailyCapacity().' clients per day',
            'privacyContact' => config('clinic.privacy_contact'),
        ]);
    }

    public function store(Request $request)
    {
        $idRequired = in_array($request->input('category'), config('clinic.id_required_categories'), true);

        $data = $request->validate([
            'last_name' => ['required', 'string', 'max:100'],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_initial' => ['nullable', 'string', 'max:5'],
            'birth_date' => ['required', 'date', 'before:today'],
            'gender' => ['required', Rule::in(config('clinic.genders'))],
            'barangay' => ['required', Rule::in(config('clinic.barangays'))],
            'category' => ['required', Rule::in(config('clinic.categories'))],
            'contact_number' => ['required', 'string', 'regex:/^[0-9]{11}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'privacy_consent' => ['accepted'],
            'id_document' => [
                $idRequired ? 'required' : 'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:'.config('clinic.max_id_document_kb'),
            ],
        ]);

        $contactKey = $this->normalizeContact($data['contact_number']);

        $todayCount = ClientRequest::where('registration_mode', 'Online')
            ->whereDate('created_at', today())
            ->count();
        if ($todayCount >= config('clinic.max_public_requests_per_day')) {
            throw ValidationException::withMessages([
                'contact_number' => 'We have reached the maximum number of requests for today. Please try again tomorrow.',
            ]);
        }

        $since = now()->subDays(config('clinic.request_cooldown_days'));
        $recent = ClientRequest::where('contact_key', $contactKey)->where('created_at', '>=', $since)->exists()
            || Appointment::where('contact_key', $contactKey)->where('created_at', '>=', $since)->exists();
        if ($recent) {
            throw ValidationException::withMessages([
                'contact_number' => 'A request with this contact number was already made recently.',
            ]);
        }

        $path = $name = $mime = null;
        if ($request->hasFile('id_document')) {
            $file = $request->file('id_document');
            $path = $file->store('id-documents', 'local');
            $name = $file->getClientOriginalName();
            $mime = $file->getMimeType();
        }

        $clientRequest = ClientRequest::create([
            'request_code' => strtoupper(Str::random(8)),
            'last_name' => $data['last_name'],
            'first_name' => $data['first_name'],
            'middle_initial' => $data['middle_initial'] ?? null,
            'birth_date' => $data['birth_date'],
            'gender' => $data['gender'],
            'barangay' => $data['barangay'],
            'category' => $data['category'],
            'contact_number' => $data['contact_number'],
            'contact_key' => $contactKey,
            'email' => $data['email'] ?? null,
            'registration_mode' => 'Online',
            'privacy_consent' => true,
            'consent_at' => now(),
            'privacy_notice_version' => config('clinic.privacy_notice_version'),
            'id_document_path' => $path,
            'id_document_name' => $name,
            'id_document_mime' => $mime,
        ]);

        return redirect()->route('requests.thanks', $clientRequest->request_code);
    }

    public function thanks(string $code)
    {
        $clientRequest = ClientRequest::where('request_code', $code)->firstOrFail();

        return view('requests.thanks', [
            'clinicName' => config('clinic.name'),
            'clientRequest' => $clientRequest,
        ]);
    }

    private function normalizeContact(string $number): string
    {
        $digits = preg_replace('/\D+/', '', $number);

        if (str_starts_with($digits, '63')) {
            $digits = '0'.substr($digits, 2);
        }

        return $digits;
    }
}