@extends('admin.admin.layouts.app')

@section('title', 'AralSipnayan')

@section('content')
    <div class="max-w-4xl mx-auto px-6 py-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Add Bulk Questions</h2>
            <p class="text-sm text-gray-600 mb-6">
                Upload a {{ strtoupper($type) }} file using the same columns as the export template.
            </p>

            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    <p class="font-medium mb-2">Import errors:</p>
                    <ul class="list-disc pl-5 space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.management.questions.bulk.upload', ['type' => $type]) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload File</label>
                    <input type="file" name="file" accept="{{ $type === 'csv' ? '.csv' : ($type === 'excel' ? '.xlsx,.xls' : '.tiff,.tif') }}"
                        class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg px-3 py-2.5">
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('admin.management.questions') }}"
                        class="px-4 py-2.5 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2.5 rounded-lg bg-blue-500 text-white hover:bg-blue-600 transition-colors">
                        Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
