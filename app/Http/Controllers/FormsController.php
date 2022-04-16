<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\Request;
use Auth;


class FormsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function create()
    {
        return view('forms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'title' => 'required|min:3|max:144',
                'expires_at' => 'required|date',
                'auth_required',

            ],
        );

        $validatedQ = $request->validate(
            [
                'question' => 'required|min:3|max:144',
                'answer_type' => 'required',
                'required',
            ],
        );

        $validatedC = $request->validate(
            [
                'choice' => 'required|min:3|max:144',
            ],
        );
        $validated["created_by"] = Auth::id();


        $form = Form::create($validated);



        $request->session()->flash('form-created', $form->title);
        return redirect()->route('dashboard', $form);
    }
}
