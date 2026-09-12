<div class="btn-group btn-group-sm" role="group">
    @php
        $hasDocuments = false;
        foreach(App\Models\UploadData::getDocumentFields() as $field => $label) {
            if($upload->$field) {
                $hasDocuments = true;
                break;
            }
        }
    @endphp

    @if($hasDocuments)
        <div class="dropdown" style="display: inline-block;">
            <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="dropdownDownload{{ $upload->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-download"></i> Download
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownDownload{{ $upload->id }}">
                @foreach(App\Models\UploadData::getDocumentFields() as $field => $label)
                    @if($upload->$field)
                        <a class="dropdown-item btn-download" href="#" data-id="{{ $upload->id }}" data-field="{{ $field }}">
                            {{ $label }}
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <button type="button" class="btn btn-sm btn-warning btn-edit" data-id="{{ $upload->id }}">
        <i class="fas fa-edit"></i> Edit
    </button>

    <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="{{ $upload->id }}">
        <i class="fas fa-trash"></i> Hapus
    </button>
</div>
