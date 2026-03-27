<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;

class UsersImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Kiểm tra nếu email đã tồn tại thì bỏ qua để tránh lỗi Duplicate
        $existingUser = User::where('email', $row['email'])->first();
        if ($existingUser) {
            return null; 
        }

        return new User([
            'name'     => $row['name'],
            'email'    => $row['email'],
            // Nếu cột role trống thì mặc định là student
            'role'     => isset($row['role']) ? strtolower($row['role']) : 'student',
            // Nếu cột password trống thì mặc định là 123456
            'password' => Hash::make($row['password'] ?? '123456'),
        ]);
    }
}