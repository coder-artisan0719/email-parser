<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Email;
use App\Services\EmailParserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class EmailController extends Controller
{
    protected $emailParserService;

    public function __construct(EmailParserService $emailParserService)
    {
        $this->emailParserService = $emailParserService;
    }
    
    public function index(): JsonResponse
    {
        $emails = Email::paginate(15);
        return response()->json($emails);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'affiliate_id' => 'required|integer',
            'envelope' => 'required|string',
            'from' => 'required|string|max:255',
            'subject' => 'required|string',
            'email' => 'required|string',
            'to' => 'required|string',
            'timestamp' => 'required|integer',
            'dkim' => 'nullable|string|max:255',
            'SPF' => 'nullable|string|max:255',
            'spam_score' => 'nullable|numeric',
            'sender_ip' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Parse the email content
        $rawText = $this->emailParserService->parseEmail($request->input('email'));

        // Create the email record with parsed content
        $data = $request->all();
        $data['raw_text'] = $rawText;
        
        $email = Email::create($data);

        return response()->json($email, 201);
    }

    public function show(string $id): JsonResponse
    {
        $email = Email::findOrFail($id);
        return response()->json($email);
    }


    public function update(Request $request, int $id): JsonResponse
    {
        $email = Email::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'affiliate_id' => 'integer',
            'envelope' => 'string',
            'from' => 'string|max:255',
            'subject' => 'string',
            'email' => 'string',
            'to' => 'string',
            'timestamp' => 'integer',
            'dkim' => 'nullable|string|max:255',
            'SPF' => 'nullable|string|max:255',
            'spam_score' => 'nullable|numeric',
            'sender_ip' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // If email content is updated, re-parse it
        if ($request->has('email') && $request->input('email') !== $email->email) {
            $rawText = $this->emailParserService->parseEmail($request->input('email'));
            $request->merge(['raw_text' => $rawText]);
        }

        $email->update($request->all());

        return response()->json($email);
    }

    public function destroy(int $id): JsonResponse
    {
        $email = Email::findOrFail($id);
        $email->delete();

        return response()->json(['message' => 'Email successfully deleted'], 200);
    }
}
