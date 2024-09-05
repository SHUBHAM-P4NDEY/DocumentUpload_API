<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Upload;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;
use Smalot\PdfParser\Parser;

class UploadController extends Controller
{
    public function extractContent(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $content = '';

        try {
            if ($extension === 'docx') {
                $phpWord = IOFactory::load($file->getRealPath());
                $content = '';

                foreach ($phpWord->getSections() as $section) {
                    foreach ($section->getElements() as $element) {
                        if (method_exists($element, 'getText')) {
                            $content .= $element->getText() . "\n";
                        }
                    }
                }
            }
            elseif ($extension === 'pdf') {
                $pdfParser = new Parser();
                $pdf = $pdfParser->parseFile($file->getRealPath());
                $content = $pdf->getText();
            }
            else {
                return response()->json(['success' => false, 'message' => 'Unsupported file type.'], 400);
            }
            return response()->json(['success' => true, 'content' => $content]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error extracting content: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'extracted_content' => 'nullable|string'
        ]);

        $file = $request->file('file');
        $originalFileName = $file->getClientOriginalName();
        $uploadPath = 'upload-doc/';

        $file->move(public_path($uploadPath), $originalFileName);

        $upload = new Upload();
        $upload->file_name = $originalFileName;
        $upload->description = $request->input('extracted_content');
        $upload->save();

        return response()->json(['success' => true, 'message' => 'File uploaded successfully']);
    }

    public function getDocuments()
    {
        $documents = Upload::all();
        return response()->json(['documents' => $documents]);
    }
}
