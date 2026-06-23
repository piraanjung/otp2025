<div class="form-group">
    <label>สังกัดหน่วยงาน / เทศบาล <span class="text-danger">*</span></label>
    <select name="org_id_fk" class="form-control" required>
        <option value="">-- กรุณาเลือก --</option>
        @foreach($organizations as $org)
            <option value="{{ $org->id }}"
                {{ (old('org_id_fk', isset($user) ? $user->org_id_fk : '') == $org->id) ? 'selected' : '' }}>
                {{ $org->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label>Username</label>
    <input type="text" name="username" class="form-control"
           value="{{ old('username', isset($user) ? $user->username : '') }}" required>
</div>

<div class="form-group">
    <label>Email</label>
    <input type="email" name="email" class="form-control"
           value="{{ old('email', isset($user) ? $user->email : '') }}" required>
</div>

<div class="form-group">
    <label>Password {{ isset($user) ? '(เว้นว่างหากไม่ต้องการเปลี่ยน)' : '*' }}</label>
    <input type="password" name="password" class="form-control" {{ isset($user) ? '' : 'required' }}>
</div>
<div class="form-group">
    <label>Confirm Password</label>
    <input type="password" name="password_confirmation" class="form-control">
</div>
