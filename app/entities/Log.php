<?php

namespace App\Entities;

use Core\AbstractDTO;

class Log extends AbstractDTO
{
    public int $log_id;
    public string $log_date;
    public ?string $fk_user_email;
    public ?int $log_code;
    public ?string $log_message;
    public ?string $log_file;
    public ?int $log_line;
    public User $user;

    /**
     * Définit les relations de l'objet.
     *
     * @return void
     */
    public function __construct()
    {
        $this->user = new User();
    }
}
