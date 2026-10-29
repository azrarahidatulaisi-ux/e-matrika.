<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'password_hash', 'role', 'facility_id', 'active', 'created_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';
    protected $returnType = 'array';
    /**
    * Retrieve a user record by username.
    */
    public function getByUsername(string $username): ?array
    {
        return $this->where('username', $username)->first();
    }

    /**
    * Create a new user with a hashed password using scrypt.
    */
    public function createUser(array $data): bool
    {
        // Expect $data: username, password, role, optional facility_id
        $password = $data['password'];
        $salt = random_bytes(16);
        $hash = sodium_crypto_pwhash_scryptsalsa208sha256(
            SODIUM_CRYPTO_PWHASH_SCRYPTSALSA208SHA256_BYTES,
            $password,
            $salt,
            SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
            SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE
        );
        $scryptHash = 'scrypt$' . base64_encode($salt) . '$' . base64_encode($hash);
        $insert = [
            'username'      => $data['username'],
            'password_hash' => $scryptHash,
            'role'          => $data['role'],
            'facility_id'   => $data['facility_id'] ?? null,
            'active'        => 1,
        ];
        return $this->insert($insert) !== false;
    }

    /**
    * List all users (admin view).
    */
    public function listAll(): array
    {
        return $this->findAll();
    }

    /**
    * Verify a plain password against a stored scrypt hash.
    */
    public function verifyPassword(string $plain, string $hash): bool
    {
        if (str_starts_with($hash, 'scrypt$')) {
            $parts = explode('$', $hash);
            $salt   = base64_decode($parts[1]);
            $stored = base64_decode($parts[2]);
            $calc = sodium_crypto_pwhash_scryptsalsa208sha256(
                strlen($stored),
                $plain,
                $salt,
                SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
                SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE
            );
            return hash_equals($stored, $calc);
        }
        return password_verify($plain, $hash);
    }
}
