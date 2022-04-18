<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Question;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;


class FormsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['create', 'fill', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        return view('forms.index', [
            'forms' => Form::where('created_by', Auth::id())->orderBy('updated_at', 'desc')->paginate(5)
        ]);
    }

    public function show(Form $form)
    {
        return view('forms.show', [
            'form' => $form

        ]);
    }

    public function edit(Form $form)
    {
        return view('forms.edit', [
            'form' => $form
        ]);
    }

    public function fill(Form $form)
    {
        return view('forms.fill', [
            'form' => $form
        ]);
    }

    public function update(Request $request, Form $form)
    {
        $validated = $request->validate(
            [
                'title' => 'required|min:3|max:144',
                'expires_at' => 'required|date|after_or_equal:today',
                'groups' => 'required|array|min:1',
                'groups.*.*.question' => 'required|min:3|max:144',
                'groups.*.*.choices.*.choice' => 'min:1|max:144',
                'groups.*.*.choices' => 'array|min:1',
            ],
        );

        $form->auth_required = $request->has('auth_required');
        $form->created_by = Auth::id();
        $question_ids = [];
        $choice_ids = [];
        foreach ($request->groups as $id => $group) {
            foreach ($group as $type => $subgroup) {
                $question = $form->questions()->findOrNew($id);
                $question->form_id = $form->id;
                $question->question = $subgroup["question"];
                $question->answer_type = $type;
                $question->required = isset($subgroup["required"]) ? 1 : 0;
                $question = $form->questions()->save($question);
                $question_ids[] = $question->id;
                if (isset($subgroup["choices"]))
                    foreach ($subgroup["choices"] as $id => $opt) {
                        $choice = $question->choices()->findOrNew($id);
                        $choice->question_id = $question->id;
                        $choice->choice = $opt["choice"];
                        $choice = $question->choices()->save($choice);
                        $choice_ids[] = $choice->id;
                    }
            }
        }

        $form->questions()->WhereNotIn('id', $question_ids)->delete();
        $form->questions()->each(function ($question) use ($choice_ids) {
            $question->choices()->WhereNotIn('id', $choice_ids)->delete();
        });

        $form->save($validated);


        return redirect()->route('forms.show', $form->id);
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
                if (isset($subgroup["choices"]))
                    foreach ($subgroup["choices"] as $opt) {
                        $choice["question_id"] = $created_question->id;
                        $choice["choice"] = $opt["choice"];
                        \App\Models\Choice::create($choice);
                    }
            }
        }

        $request->session()->flash(
            'form-created',
            url("/forms/{$form->id}")
        );
        return redirect()->route('dashboard', $form);
    }
}
