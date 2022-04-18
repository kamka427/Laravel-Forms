<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Form;
use Illuminate\Http\Request;
use Auth;


class FormController extends Controller
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

    public function fill($id)
    {




        $form = Form::findOrFail($id);
        if ($form->auth_required && !Auth::check()) {
            return redirect('/login');
        }
        //if expired
        if ($form->expired) {
            return redirect('/dashboard')->with('error', 'This form has expired');
        }

        return view('forms.fill', [
            'form' => $form
        ]);
    }

    public function response(Request $request)
    {
        // $form = Form::findOrFail($request->form_id);
        // // $user = User::findOrFail(Auth::id());

        $validated = $request->validate([
            'groups.*.*.answer' => 'required_with:groups.*.TEXTAREA',
            'groups.*.*.choice' => 'required_with:groups.*.ONE_CHOICE',
            'groups.*.*.choices' => 'array|min:1|required_with:groups.*.MULTIPLE_CHOICES',

        ]);

        // $form = Form::findOrFail($request->form_id);
        $user = Auth::user();

        foreach ($request->groups as $id => $group) {
            foreach ($group as $type => $question) {
                $answer = new Answer();
                $answer->question_id = $id;
                if ($user) {
                    $answer->user_id = $user->id;
                }
                if ($type == 'TEXTAREA') {
                    $answer->answer = $question['answer'];
                    $answer->save();
                } elseif ($type == 'ONE_CHOICE') {
                    $answer->choice_id = $question['choice'];
                    $answer->save();
                } elseif ($type == 'MULTIPLE_CHOICES') {
                    foreach ($request->question['choices'] as $choice) {
                        $answer->choice_id = $choice;
                        $answer->save();
                    }
                }
            }
        }
        return redirect('/dashboard')->with('success', 'Form submitted successfully');
    }


    public function update(Request $request, Form $form)
    {
        if (!$request->has('groups.*.ONE_CHOICE'))
            $request->groups = [];
        $validated = $request->validate(
            [
                'title' => 'required|min:3|max:144',
                'expires_at' => 'required|date|after_or_equal:today',
                'groups' => 'required|array|min:1',
                'groups.*.*.question' => 'required|min:3|max:144',
                'groups.*.*.choices.*.choice' => 'min:1|max:144',
                'groups.*.*.choices' => 'array|min:1|required_with:groups.*.onechoice',
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
                'groups.*.*.choices' => 'array|min:1|required_with:groups.*.onechoice',
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
