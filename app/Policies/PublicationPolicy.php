<?php

namespace App\Policies;

use App\Models\Publication;
use App\Models\User;

class PublicationPolicy
{
    /** Qualquer administrador pode excluir publicações (inclusive as de outros médicos). */
    public function delete(User $user, Publication $publication): bool
    {
        return $user->isAdmin();
    }
}
