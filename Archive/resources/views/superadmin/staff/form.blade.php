
<div class="row g-3">
    <div class="col-md-6 mb-3">
        <label for="username" class="form-label">Username:</label>
        <input type="text" id="username" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $staffMember->username ?? '') }}" required>
        @error('username')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="password" class="form-label">Password: <small class="text-muted">(Leave blank to keep current)</small></label>
        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror">
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-2 mb-3">
        <label for="prefix" class="form-label">Prefix:</label>
        <input type="text" id="prefix" name="prefix" class="form-control @error('prefix') is-invalid @enderror" value="{{ old('prefix', $staffMember->prefix ?? '') }}">
        @error('prefix')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-5 mb-3">
        <label for="firstname" class="form-label">First Name:</label>
        <input type="text" id="firstname" name="firstname" class="form-control @error('firstname') is-invalid @enderror" value="{{ old('firstname', $staffMember->firstname ?? '') }}" required>
        @error('firstname')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-5 mb-3">
        <label for="lastname" class="form-label">Last Name:</label>
        <input type="text" id="lastname" name="lastname" class="form-control @error('lastname') is-invalid @enderror" value="{{ old('lastname', $staffMember->lastname ?? '') }}" required>
        @error('lastname')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="email" class="form-label">Email:</label>
        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $staffMember->email ?? '') }}" required>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="line_id" class="form-label">Line ID:</label>
        <input type="text" id="line_id" name="line_id" class="form-control @error('line_id') is-invalid @enderror" value="{{ old('line_id', $staffMember->line_id ?? '') }}">
        @error('line_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="id_card" class="form-label">ID Card:</label>
        <input type="text" id="id_card" name="id_card" class="form-control @error('id_card') is-invalid @enderror" value="{{ old('id_card', $staffMember->id_card ?? '') }}">
        @error('id_card')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="phone" class="form-label">Phone:</label>
        <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $staffMember->phone ?? '') }}">
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="gender" class="form-label">Gender:</label>
        <select id="gender" name="gender" class="form-select @error('gender') is-invalid @enderror">
            <option value="">Select Gender</option>
            <option value="male" {{ old('gender', $staffMember->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ old('gender', $staffMember->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
            <option value="other" {{ old('gender', $staffMember->gender ?? '') == 'other' ? 'selected' : '' }}>Other</option>
        </select>
        @error('gender')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="status" class="form-label">Status:</label>
        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="active" {{ old('status', $staffMember->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $staffMember->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            <option value="pending" {{ old('status', $staffMember->status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 mb-3">
        <label for="address" class="form-label">Address:</label>
        <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror">{{ old('address', $staffMember->address ?? '') }}</textarea>
        @error('address')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- <div class="col-md-6 mb-3">
        <label for="organization_id" class="form-label">Organization:</label>
        <select id="organization_id" name="organization_id" class="form-select @error('organization_id') is-invalid @enderror">
            <option value="">Select Organization</option>
            @foreach ($organizations as $org)
                <option value="{{ $org->id }}" {{ old('organization_id', $staffMember->organization_id ?? '') == $org->id ? 'selected' : '' }}>
                    {{ $org->org_name }}
                </option>
            @endforeach
        </select>
        @error('organization_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div> --}}

    <div class="col-md-6 mb-3">
        <label for="zone_id" class="form-label">Zone:</label>
        <select id="zone_id" name="zone_id" class="form-select @error('zone_id') is-invalid @enderror">
            <option value="">Select Zone</option>
            @foreach ($zones as $zone)
                <option value="{{ $zone->id }}" {{ old('zone_id', $staffMember->zone_id ?? '') == $zone->id ? 'selected' : '' }}>
                    {{ $zone->zone_name }}
                </option>
            @endforeach
        </select>
        @error('zone_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="zone_block_id" class="form-label">Zone Block:</label>
        <select id="zone_block_id" name="zone_block_id" class="form-select @error('zone_block_id') is-invalid @enderror">
            <option value="">Select Zone Block</option>
            @foreach ($zoneBlocks as $zb)
                <option value="{{ $zb->id }}" {{ old('zone_block_id', $staffMember->zone_block_id ?? '') == $zb->id ? 'selected' : '' }}>
                    {{ $zb->zone_block_name }} ({{ $zb->zone->zone_name ?? 'N/A' }})
                </option>
            @endforeach
        </select>
        @error('zone_block_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="province_code" class="form-label">Province Code:</label>
        <input type="text" id="province_code" name="province_code" class="form-control @error('province_code') is-invalid @enderror" value="{{ old('province_code', $staffMember->province_code ?? '') }}">
        @error('province_code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="district_code" class="form-label">District Code:</label>
        <input type="text" id="district_code" name="district_code" class="form-control @error('district_code') is-invalid @enderror" value="{{ old('district_code', $staffMember->district_code ?? '') }}">
        @error('district_code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="tambon_code" class="form-label">Tambon Code:</label>
        <input type="text" id="tambon_code" name="tambon_code" class="form-control @error('tambon_code') is-invalid @enderror" value="{{ old('tambon_code', $staffMember->tambon_code ?? '') }}">
        @error('tambon_code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 mb-3">
        <label for="comment" class="form-label">Comment:</label>
        <textarea id="comment" name="comment" class="form-control @error('comment') is-invalid @enderror">{{ old('comment', $staffMember->comment ?? '') }}</textarea>
        @error('comment')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
