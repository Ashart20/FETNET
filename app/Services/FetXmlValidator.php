<?php

use DOMDocument;
use Exception;
public function validate(string $xmlContent): bool
{
    $xsdPath = resource_path('schemas/fet.xsd');

    libxml_use_internal_errors(true);

    $dom = new DOMDocument();

    // Pastikan XML dapat dimuat
    if (!$dom->loadXML($xmlContent)) {
        $error = libxml_get_last_error();
        libxml_clear_errors();
        throw new Exception("Invalid XML: " . ($error ? trim($error->message) : "Unknown error"));
    }

    // Validasi terhadap XSD
    if (!$dom->schemaValidate($xsdPath)) {
        $errors = libxml_get_errors();
        libxml_clear_errors();
        throw new Exception("XML Validation Failed: " . json_encode(array_map(fn($err) => trim($err->message), $errors)));
    }

    return true;
}
