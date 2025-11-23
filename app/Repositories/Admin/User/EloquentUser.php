<?php

namespace App\Repositories\Admin\User;

use App\Models\User;
use App\Support\Config\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Exception;

class EloquentUser implements UserRepository
{
    public function list(?string $search)
    {
        return User::query()
            ->with('roles')
            ->when($search, function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(Config::PAGINATE)
            ->toArray();
    }

    public function store(array $data)
    {
        DB::beginTransaction();
        try {
            $user = new User();

            $user->first_name = $data['first_name'];
            $user->last_name = $data['last_name'];
            $user->username = $data['username'];
            $user->email = $data['email'];
            $user->password = Hash::make($data['password']);
            
            // Handle profile image upload
            if (isset($data['profile']) && $data['profile']) {
                $uploadResult = $this->uploadProfileImage($data['profile']);
                $user->profile = $uploadResult; // Laravel will auto-encode to JSON
            }
            
            // Set optional fields
            $user->phone_number = $data['phone_number'] ?? null;
            $user->gender = $data['gender'] ?? null;
            $user->dob = $data['dob'] ?? null;
            $user->type = $data['type'] ?? 'user';
            $user->status = "active";

            $user->save();
            
            // Sync roles if provided
            if (isset($data['roles']) && is_array($data['roles'])) {
                $user->roles()->sync($data['roles']);
            }
            
            DB::commit();

            return $user->toArray();
        } catch (Exception $exception) {
            DB::rollBack();
            throw new Exception($exception->getMessage());
        }
    }

    public function find(int $id)
    {
        return User::findOrFail($id);
    }

    public function update(int $id, array $data)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            
            $user->first_name = $data['first_name'];
            $user->last_name = $data['last_name'];
            $user->username = $data['username'];
            $user->email = $data['email'];
            
            // Handle profile image upload if new image is provided
            if (isset($data['profile']) && $data['profile']) {
                // Delete old profile image if exists
                if ($user->profile && isset($user->profile['path'])) {
                    $this->deleteProfileImage($user->profile['path']);
                }
                
                $uploadResult = $this->uploadProfileImage($data['profile']);
                $user->profile = $uploadResult; // Laravel will auto-encode to JSON
            }
            
            // Update optional fields if provided
            if (isset($data['phone_number'])) {
                $user->phone_number = $data['phone_number'];
            }
            
            if (isset($data['gender'])) {
                $user->gender = $data['gender'];
            }
            
            if (isset($data['dob'])) {
                $user->dob = $data['dob'];
            }
            
            if (isset($data['type'])) {
                $user->type = $data['type'];
            }
            
            // Only update password if provided
            if (!empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }
            
            $user->save();
            
            // Sync roles if provided
            if (isset($data['roles']) && is_array($data['roles'])) {
                $user->roles()->sync($data['roles']);
            }
            
            DB::commit();
            
            return $user;
        } catch (Exception $exception) {
            DB::rollBack();
            throw new Exception($exception->getMessage());
        }
    }

    public function delete(int $id)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            
            // Delete profile image if exists
            if ($user->profile && isset($user->profile['path'])) {
                $this->deleteProfileImage($user->profile['path']);
            }
            
            // Revoke all Sanctum tokens for this user to prevent API access
            $user->tokens()->delete();
            
            // Delete the user
            $user->delete();
            
            DB::commit();
            return true;
        } catch (Exception $exception) {
            DB::rollBack();
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * Upload profile image to local storage
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return array
     */
    private function uploadProfileImage($file): array
    {
        // Generate unique filename
        $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        
        // Store file in storage/app/public/profiles
        $path = $file->storeAs('profiles', $filename, 'public');
        
        // Get image dimensions
        $width = null;
        $height = null;
        $mimeType = $file->getMimeType();
        
        if (strpos($mimeType, 'image/') === 0) {
            $imageSize = @getimagesize($file->getRealPath());
            $width = $imageSize[0] ?? null;
            $height = $imageSize[1] ?? null;
        }
        
        // Get public URL using the public disk
        $publicUrl = Storage::disk('public')->url($path);
        
        return [
            'path' => $publicUrl,
            'width' => $width,
            'height' => $height,
            'mime_type' => $mimeType,
            'filename' => $file->getClientOriginalName(),
        ];
    }

    /**
     * Delete profile image from local storage
     *
     * @param string $path
     * @return void
     */
    private function deleteProfileImage(string $path): void
    {
        // Extract the storage path from the public URL
        // URL format: /storage/profiles/filename.jpg
        // Storage path: profiles/filename.jpg
        if (strpos($path, '/storage/') !== false) {
            $storagePath = str_replace('/storage/', '', $path);
            if (Storage::disk('public')->exists($storagePath)) {
                Storage::disk('public')->delete($storagePath);
            }
        }
    }
}
