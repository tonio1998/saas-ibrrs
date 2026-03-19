<form method="POST" action="{{ route('users.store') }}">
    @csrf

    <div class="row g-4">

        <x-form.group name="name" label="Name" class="col-md-6">

            <x-form.input
                name="name"
                placeholder="Enter full name"
            />

        </x-form.group>


        <x-form.group name="email" label="Email" class="col-md-6">

            <x-form.input
                type="email"
                name="email"
                placeholder="Enter email"
            />

        </x-form.group>


        <x-form.group name="role" label="Role" class="col-md-6">
            <x-form.select
                name="role"
                ajax="{{ route('select2.roles') }}"
                placeholder="Select role"
                multiple
            />
        </x-form.group>


        <x-form.group name="password" label="Password" class="col-md-6">

            <x-form.password
                name="password"
                placeholder="Enter password"
            />

        </x-form.group>


        <x-form.group name="password_confirmation" label="Confirm Password" class="col-md-6">

            <x-form.password
                name="password_confirmation"
                placeholder="Confirm password"
            />

        </x-form.group>

    </div>


    <div class="form-actions mt-4 d-flex align-items-center gap-2">

        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check"></i>
            Save User
        </button>

        <a href="{{ route('users.index') }}" class="btn btn-light">
            Cancel
        </a>

    </div>

</form>
