<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Services\Mailer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class ContactController extends Controller
{
    public function __construct(private readonly Mailer $mailer)
    {
    }

    public function show(): View
    {
        return view('contact.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $contact = Contact::create($data);

        $sent = false;

        if ($this->mailer->isConfigured()) {
            try {
                $this->mailer->send([
                    'to' => config('mail.from.address'),
                    'subject' => '[HAWB Contato] '.$data['subject'],
                    'html' => $this->render($contact),
                    'text' => $this->renderPlain($contact),
                ]);

                $sent = true;
            } catch (RuntimeException) {
                // Guardamos a mensagem mesmo se o SMTP falhar: o contato não se perde.
                $sent = false;
            }
        }

        $contact->update(['notified' => $sent]);

        if ($sent) {
            return back()->with('success', 'Mensagem enviada! Entraremos em contato em breve.');
        }

        return back()->with('success',
            'Mensagem recebida e registrada com sucesso. (Envio por e-mail ainda não configurado no servidor.)');
    }

    private function render(Contact $contact): string
    {
        $name = e($contact->name);
        $email = e($contact->email);
        $subject = e($contact->subject);
        $message = e($contact->message);
        $date = $contact->created_at->format('d/m/Y H:i');

        return <<<HTML
        <div style="font-family:Arial,Helvetica,sans-serif;max-width:640px;margin:auto;color:#222">
            <h2 style="color:#009639">Nova mensagem pelo formulário de contato — HAWB</h2>
            <table style="border-collapse:collapse;width:100%">
                <tr><td style="padding:6px 0"><strong>Nome:</strong></td><td>{$name}</td></tr>
                <tr><td style="padding:6px 0"><strong>E-mail:</strong></td><td>{$email}</td></tr>
                <tr><td style="padding:6px 0"><strong>Assunto:</strong></td><td>{$subject}</td></tr>
            </table>
            <hr style="border:none;border-top:1px solid #ddd">
            <p style="white-space:pre-wrap">{$message}</p>
            <p style="color:#777;font-size:12px">Recebido em {$date}</p>
        </div>
        HTML;
    }

    private function renderPlain(Contact $contact): string
    {
        return "Nova mensagem pelo formulário de contato — HAWB\n\n"
            ."Nome: {$contact->name}\n"
            ."E-mail: {$contact->email}\n"
            ."Assunto: {$contact->subject}\n\n"
            .$contact->message;
    }
}
