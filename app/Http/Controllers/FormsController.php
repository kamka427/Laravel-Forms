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

    public function index()
    {
        return view('forms.index', [
            'forms' => Form::where('created_by', Auth::id())->orderBy('updated_at', 'desc')->paginate(5)
        ]);
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
                'expires_at' => 'required|date|after_or_equal:today',
                'groups' => 'required|array|min:1',
                'groups.*.*.question' => 'required|min:3|max:144',
                'groups.*.*.choices.*.choice' => 'min:3|max:144',
                'groups.*.*.choices' => 'array|min:2',
            ],
        );

        $validated['auth_required'] = $request->has('auth_required');
        $validated["created_by"] = Auth::id();


        $form = Form::create($validated);
        //create questions
        foreach ($request->groups as $group) {
            foreach ($group as $key => $subgroup) {
                $question["form_id"] = $form->id;
                $question["question"] = $subgroup["question"];
                if ($key == "textarea") {
                    $question["answer_type"] = "TEXTAREA";
                } elseif ($key == "onechoice") {
                    $question["answer_type"] = "ONE_CHOICE";
                } elseif ($key == "mulchoice") {
                    $question["answer_type"] = "MULTIPLE_CHOICES";
                }
                $question["required"] = isset($subgroup["required"]) ? 1 : 0;
                $created_question = \App\Models\Question::create($question);
                //create choices
                if (isset($subgroup["choices"]))
                    foreach ($subgroup["choices"] as $opt) {
                        $choice["question_id"] = $created_question->id;
                        $choice["choice"] = $opt["choice"];
                        \App\Models\Choice::create($choice);
                    }
            }
        }







        $request->session()->flash('form-created', $form);
        return redirect()->route('dashboard', $form);
    }

    public function show(Form $form)
    {
        return view('form.show', [
            'form' => $form
        ]);
    }
}
