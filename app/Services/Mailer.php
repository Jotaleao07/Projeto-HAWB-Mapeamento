<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailerException;
use RuntimeException;

/**
 * Wrapper fino do PHPMailer configurado a partir de config/mail.php.
 *
 * Variáveis relevantes no .env:
 *   MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD,
 *   MAIL_ENCRYPTION (tls|ssl|null), MAIL_FROM_ADDRESS, MAIL_FROM_NAME
 */
class Mailer
{
    /**
     * Envia um e-mail HTML.
     *
     * @param  array{to: string, subject: string, html: string, text?: string}  $payload
     * @return void
     *
     * @throws RuntimeException quando o SMTP não está configurado ou o envio falha
     */
    public function send(array $payload): void
    {
        $mail = $this->build();

        try {
            $mail->setFrom(
                config('mail.from.address'),
                config('mail.from.name'),
            );

            $mail->addAddress($payload['to']);
            $mail->Subject = $payload['subject'];
            $mail->Body = $payload['html'];
            $mail->AltBody = $payload['text'] ?? trim(strip_tags($payload['html']));

            $mail->send();
        } catch (MailerException $e) {
            throw new RuntimeException('Falha ao enviar e-mail: '.$e->getMessage(), 0, $e);
        }
    }

    /** Indica se há credenciais SMTP suficientes para tentar um envio real. */
    public function isConfigured(): bool
    {
        $transport = config('mail.mailers.smtp');

        return is_array($transport)
            && ! empty($transport['host'])
            && ! empty($transport['username'])
            && ! empty($transport['password']);
    }

    private function build(): PHPMailer
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';

        $smtp = config('mail.mailers.smtp', []);

        $mail->Host = $smtp['host'] ?? '127.0.0.1';
        $mail->Port = $smtp['port'] ?? 2525;
        $mail->SMTPAuth = ! empty($smtp['username']);
        $mail->Username = $smtp['username'] ?? '';
        $mail->Password = $smtp['password'] ?? '';
        $mail->SMTPSecure = $smtp['encryption'] ?? 'tls';
        $mail->SMTPDebug = 0;

        return $mail;
    }
}
