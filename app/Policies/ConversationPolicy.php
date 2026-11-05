<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;

class ConversationPolicy
{
    /** Apenas o paciente e o médico da conversa podem acessá-la. */
    public function view(User $user, Conversation $conversation): bool
    {
        return $conversation->involves($user);
    }
}
