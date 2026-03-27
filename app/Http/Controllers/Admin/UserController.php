<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;

class UserController extends Controller
{
    // 1. Danh sách người dùng
// Thêm Request vào tham số của hàm index
public function index(Request $request)
{
    $query = User::query();

    // Xử lý tìm kiếm nếu có nhập từ khóa
    if ($request->filled('search')) {
        $search = trim($request->search);
        $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%");
        });
    }

    // Sắp xếp mới nhất và phân trang
    // appends(request()->all()) giúp giữ từ khóa tìm kiếm khi chuyển trang (trang 2, 3...)
    $users = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->all());

    return view('admin.users.index', compact('users'));
}

    // 2. Lưu người dùng mới
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,teacher,student',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Đã thêm tài khoản thành công!');
    }

    // 3. Cập nhật thông tin (Sửa role, đổi tên, đổi mật khẩu)
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:admin,teacher,student',
        ]);

        $data = [
            'name' => $request->name,
            'role' => $request->role,
        ];

        // Nếu có nhập password mới thì mới cập nhật, không thì giữ nguyên
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật thông tin thành công!');
    }

    // 4. Xóa tài khoản
    public function destroy($id)
    {
        if ($id == auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Bạn không thể tự xóa chính mình!');
        }

        User::destroy($id);
        return redirect()->route('admin.users.index')->with('success', 'Đã xóa tài khoản.');
    }

    public function importExcel(Request $request) 
{
    $request->validate([
        'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048'
    ]);

    try {
        Excel::import(new UsersImport, $request->file('file_excel'));
        return back()->with('success', 'Đã nhập hàng loạt tài khoản thành công!');
    } catch (\Exception $e) {
        return back()->with('error', 'Lỗi dữ liệu: ' . $e->getMessage());
    }
}
}