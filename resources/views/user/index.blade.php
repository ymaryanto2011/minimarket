@extends('layouts.app')
@section('title', 'Manajemen User')
@section('page_title', 'Manajemen User')
@section('page_subtitle', 'Kelola akun pengguna dan hak akses sistem')

@section('content')
<div x-data="userPage()" class="space-y-6">

    {{-- ===== Add User ===== --}}
    <div class="card">
        <div class="card-header">Tambah User Baru</div>
        <form method="POST" action="{{ route('users.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" class="input-field" required placeholder="Nama pengguna">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" class="input-field" required placeholder="email@minimarket.local">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Level <span class="text-red-500">*</span></label>
                <select name="role" class="input-field" required>
                    <option value="cashier" {{ old('role')=='cashier'?'selected':'' }}>Kasir</option>
                    <option value="supervisor" {{ old('role')=='supervisor'?'selected':'' }}>Supervisor</option>
                    <option value="admin" {{ old('role')=='admin'?'selected':'' }}>Admin</option>
                </select>
            </div>
            <button type="submit" class="btn-primary h-10">
                <svg class="w-4 h-4 inline -mt-0.5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah User
            </button>
        </form>
        <p class="text-xs text-gray-400 mt-2">Password default: <strong>kosong</strong>. User dapat login tanpa password. Admin dapat mereset password kapan saja.</p>
    </div>

    {{-- ===== User Table ===== --}}
    <div class="card">
        <div class="card-header">Daftar Pengguna ({{ $users->count() }})</div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b bg-gray-50">
                        <th class="text-left py-3 px-3 font-semibold text-gray-600">Nama</th>
                        <th class="text-left py-3 px-3 font-semibold text-gray-600">Email</th>
                        <th class="text-left py-3 px-3 font-semibold text-gray-600">Level</th>
                        <th class="text-center py-3 px-3 font-semibold text-gray-600">Status</th>
                        <th class="text-center py-3 px-3 font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition {{ $user->id === auth()->id() ? 'bg-blue-50/50' : '' }}">
                        <td class="py-3 px-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br
                                    {{ $user->role === 'admin' ? 'from-red-400 to-rose-600' : ($user->role === 'supervisor' ? 'from-yellow-400 to-amber-600' : 'from-green-400 to-emerald-600') }}
                                    flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <span class="font-medium text-gray-800">{{ $user->name }}</span>
                                    @if($user->id === auth()->id())
                                    <span class="ml-1 text-xs text-blue-500">(Anda)</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-3 text-gray-600">{{ $user->email }}</td>
                        <td class="py-3 px-3">
                            @if($user->role === 'admin')
                            <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
                                </svg>
                                Admin
                            </span>
                            @elseif($user->role === 'supervisor')
                            <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-700 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                </svg>
                                Supervisor
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                                Kasir
                            </span>
                            @endif
                        </td>
                        <td class="py-3 px-3 text-center">
                            @if($user->is_active)
                            <span class="bg-green-100 text-green-700 text-xs font-medium px-2 py-0.5 rounded-full">Aktif</span>
                            @else
                            <span class="bg-gray-100 text-gray-500 text-xs font-medium px-2 py-0.5 rounded-full">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="py-3 px-3">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Edit --}}
                                <button @click="openEdit({{ json_encode(['id'=>$user->id,'name'=>$user->name,'email'=>$user->email,'role'=>$user->role,'is_active'=>$user->is_active]) }})"
                                    class="text-blue-600 hover:text-blue-800 p-1.5 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                {{-- Reset Password --}}
                                <form method="POST" action="{{ route('users.resetPassword', $user) }}"
                                    onsubmit="return confirm('Reset password {{ addslashes($user->name) }} ke kosong?')">
                                    @csrf
                                    <button type="submit" class="text-amber-600 hover:text-amber-800 p-1.5 hover:bg-amber-50 rounded-lg transition" title="Reset Password ke Kosong">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                        </svg>
                                    </button>
                                </form>
                                {{-- Set Custom Password --}}
                                <button @click="openSetPwd({{ json_encode(['id'=>$user->id,'name'=>$user->name]) }})"
                                    class="text-purple-600 hover:text-purple-800 p-1.5 hover:bg-purple-50 rounded-lg transition" title="Set Password Baru">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </button>
                                {{-- Delete --}}
                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('users.destroy', $user) }}"
                                    onsubmit="return confirm('Hapus user {{ addslashes($user->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 p-1.5 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-400">Belum ada user</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== Edit Modal ===== --}}
    <div x-show="editOpen" x-cloak
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
        @click.self="editOpen=false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Edit User</h3>
            <form method="POST" :action="`/users/${editData.id}`">
                @csrf @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                        <input type="text" name="name" :value="editData.name" class="input-field" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" :value="editData.email" class="input-field" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Level</label>
                        <select name="role" class="input-field" :value="editData.role">
                            <option value="cashier" :selected="editData.role==='cashier'">Kasir</option>
                            <option value="supervisor" :selected="editData.role==='supervisor'">Supervisor</option>
                            <option value="admin" :selected="editData.role==='admin'">Admin</option>
                        </select>
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" :checked="editData.is_active" class="w-4 h-4 rounded">
                        <span class="text-sm text-gray-700">Akun Aktif</span>
                    </label>
                </div>
                <div class="flex gap-2 mt-5">
                    <button type="button" @click="editOpen=false" class="btn-secondary flex-1">Batal</button>
                    <button type="submit" class="btn-primary flex-1">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== Set Password Modal ===== --}}
    <div x-show="pwdOpen" x-cloak
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
        @click.self="pwdOpen=false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-1">Set Password Baru</h3>
            <p class="text-sm text-gray-500 mb-4">Untuk user: <strong x-text="pwdData.name"></strong></p>
            <form method="POST" :action="`/users/${pwdData.id}/set-password`">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                        <input type="password" name="new_password" class="input-field" placeholder="Kosongkan = password kosong">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi</label>
                        <input type="password" name="new_password_confirmation" class="input-field" placeholder="Ulangi password">
                    </div>
                </div>
                <div class="flex gap-2 mt-5">
                    <button type="button" @click="pwdOpen=false" class="btn-secondary flex-1">Batal</button>
                    <button type="submit" class="btn-primary flex-1">Simpan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@section('extra_js')
<script>
    function userPage() {
        return {
            editOpen: false,
            editData: {},
            pwdOpen: false,
            pwdData: {},
            openEdit(data) {
                this.editData = data;
                this.editOpen = true;
            },
            openSetPwd(data) {
                this.pwdData = data;
                this.pwdOpen = true;
            },
        };
    }
</script>
@endsection