<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Request an appointment · {{ $clinicName }}</title>
    <style>
        body { margin:0; padding:48px 20px; font-family: Arial, sans-serif; background:#f1f5f9; color:#1e293b; }
        main { max-width:720px; margin:auto; padding:32px; background:#fff; border-radius:16px; }
        h1 { color:#0f766e; }
        label { display:block; margin-top:16px; font-weight:bold; }
        input, select { width:100%; padding:10px; margin-top:6px; border:1px solid #cbd5e1; border-radius:8px; box-sizing:border-box; }
        .error { color:#b91c1c; font-size:14px; margin-top:4px; }
        .check { display:flex; gap:8px; align-items:flex-start; font-weight:normal; }
        .check input { width:auto; margin-top:4px; }
        button { margin-top:24px; padding:12px 24px; background:#0f766e; color:#fff; border:0; border-radius:8px; font-size:16px; cursor:pointer; }
    </style>
</head>
<body>
<main>
    <h1>Request an appointment</h1>
    <p>{{ $scheduleSummary }}</p>

    <form method="POST" action="{{ route('requests.store') }}" enctype="multipart/form-data">
        @csrf

        @foreach ([['last_name','Last name'],['first_name','First name'],['middle_initial','Middle initial']] as [$field,$label])
            <label>{{ $label }}
                <input type="text" name="{{ $field }}" value="{{ old($field) }}">
            </label>
            @error($field)<div class="error">{{ $message }}</div>@enderror
        @endforeach

        <label>Birth date
            <input type="date" name="birth_date" value="{{ old('birth_date') }}">
        </label>
        @error('birth_date')<div class="error">{{ $message }}</div>@enderror

        <label>Gender
            <select name="gender">
                <option value="">Select…</option>
                @foreach ($genders as $g)
                    <option value="{{ $g }}" @selected(old('gender') === $g)>{{ $g }}</option>
                @endforeach
            </select>
        </label>
        @error('gender')<div class="error">{{ $message }}</div>@enderror

        <label>Barangay
            <select name="barangay">
                <option value="">Select…</option>
                @foreach ($barangays as $b)
                    <option value="{{ $b }}" @selected(old('barangay') === $b)>{{ $b }}</option>
                @endforeach
            </select>
        </label>
        @error('barangay')<div class="error">{{ $message }}</div>@enderror

        <label>Category
            <select name="category" id="category">
                <option value="">Select…</option>
                @foreach ($categories as $c)
                    <option value="{{ $c }}" @selected(old('category') === $c)>{{ $c }}</option>
                @endforeach
            </select>
        </label>
        @error('category')<div class="error">{{ $message }}</div>@enderror

        <div id="id-upload" style="display:none">
            <label>Upload ID (photo or PDF, max 10 MB)
                <input type="file" name="id_document" accept=".jpg,.jpeg,.png,.webp,.pdf">
            </label>
            @error('id_document')<div class="error">{{ $message }}</div>@enderror
        </div>

        <label>Contact number
            <input type="tel" name="contact_number" value="{{ old('contact_number') }}">
        </label>
        @error('contact_number')<div class="error">{{ $message }}</div>@enderror

        <label>Email (optional)
            <input type="email" name="email" value="{{ old('email') }}">
        </label>
        @error('email')<div class="error">{{ $message }}</div>@enderror

        <label class="check">
            <input type="checkbox" name="privacy_consent" value="1" @checked(old('privacy_consent'))>
            <span>I agree to the collection and use of my personal information for scheduling. Questions: contact {{ $privacyContact }}.</span>
        </label>
        @error('privacy_consent')<div class="error">{{ $message }}</div>@enderror

        <button type="submit">Submit request</button>
    </form>
</main>

<script>
    const idRequired = @json($idRequired);
    const category = document.getElementById('category');
    const box = document.getElementById('id-upload');
    const toggle = () => box.style.display = idRequired.includes(category.value) ? 'block' : 'none';
    category.addEventListener('change', toggle);
    toggle();
</script>
</body>
</html>