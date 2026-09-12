<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception ketika data sekolah tidak ditemukan
 */
class RincianSekolahNotFoundException extends Exception
{
    public function __construct($message = "Data rincian sekolah tidak ditemukan")
    {
        parent::__construct($message, 404);
    }
}

/**
 * Exception ketika menu tidak valid
 */
class InvalidMenuException extends Exception
{
    public function __construct($message = "Menu tidak valid atau tidak ditemukan")
    {
        parent::__construct($message, 422);
    }
}

/**
 * Exception ketika file upload gagal
 */
class FileUploadException extends Exception
{
    public function __construct($message = "Gagal upload file")
    {
        parent::__construct($message, 500);
    }
}

/**
 * Exception ketika validasi dokumen gagal
 */
class DocumentValidationException extends Exception
{
    public function __construct($message = "Validasi dokumen gagal")
    {
        parent::__construct($message, 422);
    }
}
