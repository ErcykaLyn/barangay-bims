<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;

class DocumentPrintController extends Controller
{
    /**
     * Generate printable document (Word format) based on document type.
     */
    public function print($id)
    {
        // Kunin ang Service Request at ang kaakibat na Resident
        $serviceRequest = ServiceRequest::with('resident')->findOrFail($id);

        // Check kung pwede i-print
        if (!in_array($serviceRequest->status, ['accepted', 'ready_for_pickup', 'completed'])) {
            abort(403, 'Only ACCEPTED, READY-FOR-PICKUP, or COMPLETED requests can be printed.');
        }

        $resident = $serviceRequest->resident;

        // ✅ I-map ang document type sa tamang Word template filename
        $templateFile = match ($serviceRequest->document_type) {
            'Barangay Residency Certificate'    => 'brgy_residency.docx',
            'Barangay Indigency Certificate'    => 'brgy_indigency.docx',
            'Barangay Clearance Certification'  => 'brgy_clearance.docx',
            'Good Moral Certificate'            => 'good_moral.docx',
            'Barangay Business Clearance'       => 'business_clearance.docx',
            default                             => abort(404, 'Document template not found.'),
        };

        $templatePath = storage_path('app/templates/' . $templateFile);

        // Check kung existing ang file
        if (!file_exists($templatePath)) {
            abort(500, 'Template file not found: ' . $templateFile);
        }

        // I-load ang Word Template
        $template = new TemplateProcessor($templatePath);

        // I-prepare ang mga data na ipapalit sa tags
        $fullName = strtoupper($resident->first_name . ' ' . ($resident->middle_name ?? '') . ' ' . $resident->last_name);
        $address = $resident->address ?? 'N/A';
        
        // Kung may specific na purpose ang request, gamitin yun. Kung wala, default.
        $purpose = $serviceRequest->purpose ?? 'WHATEVER LEGAL PURPOSE IT MAY SERVE';
        
        // Kung Business Clearance, kunin ang business name (kung meron sa request)
        // Note: Kung wala kang business_name column sa service_requests table, 
        // pwede mong gamitin ang $serviceRequest->purpose o tanggalin na lang ito.
        $businessName = $serviceRequest->business_name ?? 'N/A'; 

        // ✅ Palitan ang mga Tags sa Word
        $template->setValue('RESIDENT_NAME', $fullName);
        $template->setValue('RESIDENT_ADDRESS', $address);
        $template->setValue('DATE_ISSUED', date('F j, Y'));
        $template->setValue('PURPOSE', strtoupper($purpose));
        $template->setValue('BUSINESS_NAME', strtoupper($businessName)); 
        
        // Optional: Kung may Control Number or Reference Number ka sa ServiceRequest
        // $template->setValue('REFERENCE_NO', $serviceRequest->reference_number ?? 'N/A');

        // I-save ang bagong Word file
        $safeLastName = str_replace(' ', '_', $resident->last_name);
        $fileName = 'Document_' . $safeLastName . '_' . date('Ymd') . '.docx';
        
        $tempFile = tempnam(sys_get_temp_dir(), 'docx');
        $template->saveAs($tempFile);

        // I-download sa browser
        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}