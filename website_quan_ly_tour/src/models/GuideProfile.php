<?php

class GuideProfile
{
    public $id;
    public $user_id;
    public $birthdate;
    public $avatar;
    public $phone;
    public $certificate;
    public $languages;
    public $experience;
    public $history;
    public $rating;
    public $health_status;
    public $group_type;
    public $specialty;
    public $created_at;
    public $updated_at;

    public function __construct($data = [])
    {
        if (is_array($data)) {
            $this->id = $data['id'] ?? null;
            $this->user_id = $data['user_id'] ?? null;
            $this->birthdate = $data['birthdate'] ?? '';
            $this->avatar = $data['avatar'] ?? '';
            $this->phone = $data['phone'] ?? '';
            $this->certificate = $data['certificate'] ?? '';
            $this->languages = $data['languages'] ?? '';
            $this->experience = $data['experience'] ?? '';
            $this->history = $data['history'] ?? '';
            $this->rating = $data['rating'] ?? 0;
            $this->health_status = $data['health_status'] ?? '';
            $this->group_type = $data['group_type'] ?? '';
            $this->specialty = $data['specialty'] ?? '';
            $this->created_at = $data['created_at'] ?? null;
            $this->updated_at = $data['updated_at'] ?? null;
        }
    }

    public static function findByUserId($user_id)
    {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM guide_profiles WHERE user_id = :user_id LIMIT 1");
        $stmt->execute(['user_id' => $user_id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new self($data) : null;
    }

    public static function createOrUpdate($user_id, $data)
    {
        $pdo = getDB();
        
        // Kiểm tra xem hồ sơ đã tồn tại hay chưa
        $existing = self::findByUserId($user_id);
        
        if ($existing) {
            // Update
            $stmt = $pdo->prepare("
                UPDATE guide_profiles SET
                    birthdate = :birthdate,
                    avatar = :avatar,
                    phone = :phone,
                    certificate = :certificate,
                    languages = :languages,
                    experience = :experience,
                    history = :history,
                    health_status = :health_status,
                    group_type = :group_type,
                    specialty = :specialty,
                    updated_at = NOW()
                WHERE user_id = :user_id
            ");
        } else {
            // Create
            $stmt = $pdo->prepare("
                INSERT INTO guide_profiles (
                    user_id, birthdate, avatar, phone, certificate, 
                    languages, experience, history, health_status, 
                    group_type, specialty, created_at, updated_at
                ) VALUES (
                    :user_id, :birthdate, :avatar, :phone, :certificate,
                    :languages, :experience, :history, :health_status,
                    :group_type, :specialty, NOW(), NOW()
                )
            ");
        }

        return $stmt->execute([
            'user_id' => $user_id,
            'birthdate' => $data['birthdate'] ?? '',
            'avatar' => $data['avatar'] ?? '',
            'phone' => $data['phone'] ?? '',
            'certificate' => $data['certificate'] ?? '',
            'languages' => $data['languages'] ?? '',
            'experience' => $data['experience'] ?? '',
            'history' => $data['history'] ?? '',
            'health_status' => $data['health_status'] ?? '',
            'group_type' => $data['group_type'] ?? '',
            'specialty' => $data['specialty'] ?? '',
        ]);
    }
}
?>
