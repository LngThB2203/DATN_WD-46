<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ContactNotification;

class ContactController extends Controller
{
    /**
     * Display the contact form.
     */
    public function index()
    {
        return view('client.contact');
    }

    /**
     * Store a newly created contact message.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|min:10',
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'message.required' => 'Vui lòng nhập nội dung',
            'message.min' => 'Nội dung phải có ít nhất 10 ký tự',
        ]);

        try {
            // Save to database
            $contact = Contact::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'subject' => $request->subject,
                'message' => $request->message,
                'status' => 'new',
            ]);

            // Send email notification
            try {
                Mail::to(config('mail.from.address', 'admin@example.com'))->send(
                    new ContactNotification($contact)
                );
            } catch (\Exception $e) {
                Log::error('Failed to send contact email: ' . $e->getMessage());
                // Continue even if email fails
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất có thể.'
                ]);
            }

            return back()->with('success', 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất có thể.');
        } catch (\Exception $e) {
            Log::error('Contact form error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra. Vui lòng thử lại sau.'
                ], 500);
            }

            return back()->with('error', 'Có lỗi xảy ra. Vui lòng thử lại sau.');
        }
    }

    /**
     * Display a listing of contacts (Admin).
     */
    public function adminIndex(Request $request)
    {
        $query = Contact::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if (in_array($sortBy, ['name', 'email', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $contacts = $query->paginate(15)->withQueryString();
        $stats = [
            'total' => Contact::count(),
            'new' => Contact::where('status', 'new')->count(),
            'read' => Contact::where('status', 'read')->count(),
            'replied' => Contact::where('status', 'replied')->count(),
        ];

        return view('admin.contacts.list', compact('contacts', 'stats'));
    }

    /**
     * Display the specified contact (Admin).
     */
    public function adminShow(Contact $contact)
    {
        // Mark as read if it's new
        if ($contact->status === 'new') {
            $contact->update(['status' => 'read']);
        }

        return view('admin.contacts.show', compact('contact'));
    }

    /**
     * Update contact status (Admin).
     */
    public function adminUpdateStatus(Request $request, Contact $contact)
    {
        $request->validate([
            'status' => 'required|in:new,read,replied,archived',
        ]);

        $contact->update([
            'status' => $request->status,
            'replied_at' => $request->status === 'replied' ? now() : $contact->replied_at,
        ]);

        return back()->with('success', 'Trạng thái đã được cập nhật!');
    }

    /**
     * Update admin notes (Admin).
     */
    public function adminUpdateNotes(Request $request, Contact $contact)
    {
        $request->validate([
            'admin_notes' => 'nullable|string',
        ]);

        $contact->update([
            'admin_notes' => $request->admin_notes,
        ]);

        return back()->with('success', 'Ghi chú đã được lưu!');
    }

    /**
     * Remove the specified contact (Admin).
     */
    public function adminDestroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('admin.contacts.index')->with('success', 'Liên hệ đã được xóa!');
    }
}
