@extends('layouts.staff')

@section('title', 'Waiting requests')

@section('content')
    <h1>Waiting requests</h1>
    <p>{{ $requests->total() }} request(s) waiting for a schedule. Requests appear oldest first.</p>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th scope="col">Queue position</th>
                    <th scope="col">Request code</th>
                    <th scope="col">Client</th>
                    <th scope="col">Barangay</th>
                    <th scope="col">Category</th>
                    <th scope="col">Contact number</th>
                    <th scope="col">Submitted</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($requests as $clientRequest)
                    <tr>
                        <td>{{ $requests->firstItem() + $loop->index }}</td>
                        <td>{{ $clientRequest->request_code }}</td>
                        <td>{{ $clientRequest->last_name }}, {{ $clientRequest->first_name }} {{ $clientRequest->middle_initial }}</td>
                        <td>{{ $clientRequest->barangay }}</td>
                        <td>{{ $clientRequest->category }}</td>
                        <td>{{ $clientRequest->contact_number }}</td>
                        <td>{{ $clientRequest->created_at->timezone('Asia/Manila')->format('M d, Y h:i A') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7">No requests are waiting for a schedule.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($requests->hasPages())
        <nav class="pagination" aria-label="Request queue pages">
            @if ($requests->previousPageUrl())
                <a href="{{ $requests->previousPageUrl() }}">Previous</a>
            @endif
            <span>Page {{ $requests->currentPage() }} of {{ $requests->lastPage() }}</span>
            @if ($requests->nextPageUrl())
                <a href="{{ $requests->nextPageUrl() }}">Next</a>
            @endif
        </nav>
    @endif
@endsection
