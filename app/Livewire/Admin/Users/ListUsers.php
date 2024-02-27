<?php

namespace App\Livewire\Admin\Users;

use Livewire\Component;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
//use Illuminate\Support\Facades\Session;

class ListUsers extends Component
{
    // public $name;
    // public $email;
    // public $password;
    // public $password_confirmation;
    public $state=[];

    public $showEditModal = false;
    public $user;
    public $userIdBeingRemoved = null;

    public function addNew()
    {
        $this->state = [];
        $this->showEditModal = false;
        $this->dispatch('show-form');
    }



    public function createUser()
    {
        $validateInfo=Validator::make($this->state,[
            'name'=> 'required',
            'email'=> 'required|email|unique:users',
            'password'=> 'sometimes|confirmed',
        ])->validate();
        User::create($validateInfo);
        //session()->flash('success','User added Successfully');
        $this->dispatch('hide-form', ['message' => 'User added successfully!']);
        //return redirect()->back();
        //dd($validateInfo);
    }

    public function edit(User $user)
    {
        $this->showEditModal = true;
        $this->user = $user;
        $this->state = $user->toArray();
        //dd($user->toArray());

        $this->dispatch('show-form');
    }

    public function updateUser()
    {
        $validateInfo=Validator::make($this->state,[
            'name'=> 'required',
            'email'=> 'required|email|unique:users,email,'.$this->user->id,
            'password'=> 'required|confirmed',
        ])->validate();
        $this->user->update($validateInfo);
        $this->dispatch('hide-form', ['message' => 'User updated successfully!']);
        //return redirect()->back();
    }

    public function confirmUserRemoval($userId)
    {
        $this->userIdBeingRemoved = $userId;
        //dd($this->userIdBeingRemoved);
        $this->dispatch('show-delete-modal');
    }

    public function deleteUser()
    {
        $user=User::findOrFail($this->userIdBeingRemoved);
        $user->delete();
        $this->dispatch('hide-delete-modal',['message' => 'User deleted successfully']);
    }

    public function render()
    {
        $users=User::latest()->paginate();
        return view('livewire.admin.users.list-users',['users'=>$users]);
    }
}
