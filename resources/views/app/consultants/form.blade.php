<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div><label class="block mb-1">Name</label><input name="name"
            value="{{ old('name',$consultant->user->name ?? '') }}" class="w-full border rounded px-3 py-2"
            required>@error('name')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror</div>
    <div><label class="block mb-1">Email</label><input type="email" name="email"
            value="{{ old('email',$consultant->user->email ?? '') }}" class="w-full border rounded px-3 py-2"
            required>@error('email')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror</div>
    @if(!isset($consultant))<div><label class="block mb-1">Password</label><input type="password" name="password"
            class="w-full border rounded px-3 py-2" required></div>
    <div><label class="block mb-1">Confirm Password</label><input type="password" name="password_confirmation"
            class="w-full border rounded px-3 py-2" required></div>@endif<div><label
            class="block mb-1">Specialty</label><input name="specialty"
            value="{{ old('specialty',$consultant->specialty ?? '') }}" class="w-full border rounded px-3 py-2"></div>
    <div class="flex items-end gap-2"><input type="checkbox" name="is_active" value="1"
            @checked(old('is_active',$consultant->is_active ?? true))><label>Active</label></div>
</div>