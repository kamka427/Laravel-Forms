<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Form;
use Auth;
use DateTime;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['create', 'home', 'store', 'edit', 'update', 'destroy', 'restore']);
    }

    public function home()
    {
        return view('forms.home');
    }

    public function index()
    {
        return view('forms.index', [
            'forms' => Form::where('created_by', Auth::id())->orderBy('updated_at', 'desc')->withTrashed()->paginate(5),
        ]);
    }

    public function show($id)
    {
        $form = Form::findOrFail($id);
        if ($form->created_by !== Auth::id()) {
            abort(401, 'Nincs jogosultságod a megtekinteni ezt a formot!');
        }

        return view('forms.show', [
            'form' => $form,
        ]);
    }

    public function edit($id)
    {

        $form = Form::findOrFail($id);

        if ($form->created_by !== Auth::id()) {
            abort(401, 'Nincs jogosultságod a szerkeszteni ezt a formot!');
        }

        $hasAnswers = false;
        $questions = $form->questions;
        foreach ($questions as $question) {
            $answers = $question->answers;
            if (count($answers) > 0) {
                $hasAnswers = true;
                break;
            }
        }

        if ($hasAnswers) {
            abort(403, 'Nem lehet módosítani ezt a formot, mert már kitöltötték!');
        }

        return view('forms.create-update', [
            'form' => $form,
        ]);
    }

    public function fill($id)
    {
        $form = Form::findOrFail($id);
        if ($form->auth_required && !Auth::check()) {
            return redirect('/login');
        }

        if (new DateTime($form->expires_at) < new DateTime()) {
            return view('forms.fill', [
                'form' => $form,
                'expired' => $form->expires_at,
            ]);
        }

        return view('forms.fill', [
            'form' => $form,
        ]);
    }

    public function response(Request $request, $id)
    {
        $validated = $request->validate([
            'groups.*.1.*.answer' => 'required_with:groups.*.1.TEXTAREA|min:2|max:144',
            'groups.*.*.*.answer' => 'nullable|min:2|max:144',
            'groups.*.1.*.choice' => 'required_with:groups.*.1.ONE_CHOICE|numeric|exists:choices,id',
            'groups.*.*.*.choice' => 'nullable|numeric|exists:choices,id',
            'groups.*.1.*.choices' => 'required_with:groups.*.1.MULTIPLE_CHOICES|min:1|exists:choices,id',
            'groups.*.*.*.choices' => 'nullable|array|min:1|exists:choices,id',
        ]);

        $form = Form::findOrFail($id);

        if (new DateTime($form->expires_at) < new DateTime(now())) {
            return redirect()->back()->with('expired', $form->expires_at);
        }

        //Megnézzük nem lett-e a kérdésre nem manipulálható válasz megadva
        for ($i = 0; $i < count($form->questions); $i++) {
            $question = $form->questions()->get()[$i];
            if ($question->answer_type === 'ONE_CHOICE') {
                if ($question->required) {
                    $choice = $request->groups[$i]['1']['ONE_CHOICE']['choice'];
                    if (!$question->choices()->find($choice)) {
                        abort(403, 'Nem létező válasz!');
                    }
                } else {
                    $choice = $request->groups[$question->id]['0']['ONE_CHOICE']['choice'] ?? null;
                    if ($choice !== null && !$question->choices()->find($choice)) {
                        abort(403, 'Nem létező válasz!');
                    }
                }
            } elseif ($question->answer_type === 'MULTIPLE_CHOICES') {
                if ($question->required) {
                    $choices = $request->groups[$question->id]['1']['MULTIPLE_CHOICES']['choices'];
                    foreach ($choices as $choice) {
                        if (!$question->choices()->find($choice)) {
                            abort(403, 'Nem létező válasz!');
                        }
                    }
                } else {
                    $choices = $request->groups[$question->id]['0']['MULTIPLE_CHOICES']['choices'] ?? null;
                    if ($choices !== null) {
                        foreach ($choices as $choice) {
                            if (!$question->choices()->find($choice)) {
                                abort(403, 'Nem létező válasz!');
                            }
                        }
                    }
                }
            }
        }

        //Menézzük, hogy manipulálva lett-e a válasz azzal, hogy opcionális és kötelező mezőként manipuláljuk a választ ONE_CHOICE típusú mezőknél.
        for ($i = 0; $i < count($form->questions); $i++) {
            $question = $form->questions()->get()[$i];
            $choice1 = $request->groups[$question->id]['0']['ONE_CHOICE']['choice'] ?? null;
            $choice2 = $request->groups[$question->id]['1']['ONE_CHOICE']['choice'] ?? null;
            if ($question->answer_type === 'ONE_CHOICE' && $choice1 !== null && $choice2 !== null) {
                abort(403, 'A kérdésnek egy választ kell megadnia!');
            } elseif ($question->answer_type === 'ONE_CHOICE' && $question->required && $choice1 !== null) {
                abort(403, 'A kérdésnek egy választ kell megadnia!');
            } elseif ($question->answer_type === 'ONE_CHOICE' && !$question->required && $choice2 !== null) {
                abort(403, 'A kérdésnek egy választ kell megadnia!');
            }
        }


        $user = Auth::user() ?? null;

        foreach ($validated['groups'] as $id => $req) {
            foreach ($req as $group) {
                foreach ($group as $type => $question) {
                    $answer = new Answer();
                    $answer->question_id = $id;
                    if ($user) {
                        $answer->user_id = $user->id;
                    }
                    if ($type === 'TEXTAREA') {
                        if (strlen($question['answer']) != 0) {
                            $answer->answer = $question['answer'];
                            $answer->save();
                        }
                    } elseif ($type === 'ONE_CHOICE') {
                        $answer->choice_id = $question['choice'];
                        $answer->save();
                    } elseif ($type === 'MULTIPLE_CHOICES') {
                        foreach ($question['choices'] as $choice) {
                            $answer->choice_id = $choice;
                            $answer->save();
                        }
                    }
                }
            }
        }

        return redirect('/')->with('form-filled', $form->title);
    }

    public function update(Request $request, $id)
    {
        $form = Form::findOrFail($id);
        if ($form->created_by !== Auth::id()) {
            abort(401, 'Nincs jogosultságod a szerkeszteni ezt a formot!');
        }

        $hasAnswers = false;
        $questions = $form->questions;
        foreach ($questions as $question) {
            $answers = $question->answers;
            if (count($answers) > 0) {
                $hasAnswers = true;
                break;
            }
        }

        if ($hasAnswers) {
            abort(403, 'Nem lehet módosítani ezt a formot, mert már kitöltötték!');
        }

        $validated = $request->validate(
            [
                'title' => 'required|min:3|max:144',
                'auth_required' => 'nullable|boolean',
                'expires_at' => 'required|date|after_or_equal:today',
                'groups' => 'required|array|min:1',
                'groups.*.*.question' => 'required|min:3|max:144',
                'groups.*.*.choices.*.choice' => 'min:1|max:144',
                'groups.*.*.choices' => 'array|min:1|required_with:groups.*.ONE_CHOICE,groups.*.MULTIPLE_CHOICES',
            ]
        );
        $form->auth_required = $request->has('auth_required');
        $form->created_by = Auth::id();
        $form->update($validated);

        $question_ids = [];
        $choice_ids = [];
        foreach ($validated['groups'] as $id => $group) {
            foreach ($group as $type => $subgroup) {
                $question = $form->questions()->findOrNew($id);
                $question->form_id = $form->id;
                $question->question = $subgroup['question'];
                $question->answer_type = $type;
                $question->required = isset($subgroup['required']) ? 1 : 0;
                $question = $form->questions()->save($question);
                $question_ids[] = $question->id;
                if (isset($subgroup['choices'])) {
                    foreach ($subgroup['choices'] as $id => $opt) {
                        $choice = $question->choices()->findOrNew($id);
                        $choice->question_id = $question->id;
                        $choice->choice = $opt['choice'];
                        $choice = $question->choices()->save($choice);
                        $choice_ids[] = $choice->id;
                    }
                }
            }
        }

        $form->questions()->WhereNotIn('id', $question_ids)->delete();
        $form->questions()->each(function ($question) use ($choice_ids) {
            $question->choices()->WhereNotIn('id', $choice_ids)->delete();
        });

        return redirect()->route('forms.show', $form->id);
    }

    public function create()
    {
        return view('forms.create-update');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'title' => 'required|min:3|max:144',
                'auth_required' => 'nullable|boolean',
                'expires_at' => 'required|date|after_or_equal:today',
                'groups' => 'required|array|min:1',
                'groups.*.*.question' => 'required|min:3|max:144',
                'groups.*.*.choices.*.choice' => 'required|min:3|max:144',
                'groups.*.*.choices' => 'array|min:1|required_with:groups.*.ONE_CHOICE,groups.*.MULTIPLE_CHOICES',
            ],
        );

        $validated['auth_required'] = $request->has('auth_required');
        $validated['created_by'] = Auth::id();



        $form = new Form($validated);
        $form->auth_required = $request->has('auth_required');
        $form->created_by = Auth::id();
        $form->save($validated);
        $question_ids = [];
        $choice_ids = [];
        foreach ($validated['groups'] as $id => $group) {
            foreach ($group as $type => $subgroup) {
                $question = $form->questions()->findOrNew($id);
                $question->form_id = $form->id;
                $question->question = $subgroup['question'];
                $question->answer_type = $type;
                $question->required = isset($subgroup['required']) ? 1 : 0;
                $question = $form->questions()->save($question);
                $question_ids[] = $question->id;
                if (isset($subgroup['choices'])) {
                    foreach ($subgroup['choices'] as $id => $opt) {
                        $choice = $question->choices()->findOrNew($id);
                        $choice->question_id = $question->id;
                        $choice->choice = $opt['choice'];
                        $choice = $question->choices()->save($choice);
                        $choice_ids[] = $choice->id;
                    }
                }
            }
        }

        $form->questions()->WhereNotIn('id', $question_ids)->delete();
        $form->questions()->each(function ($question) use ($choice_ids) {
            $question->choices()->WhereNotIn('id', $choice_ids)->delete();
        });

        $request->session()->flash(
            'form-created',
            url("/forms/{$form->id}/fill")
        );

        return redirect()->route('forms.home', $form);
    }
    public function destroy($id)
    {
        $form = Form::findOrFail($id);
        if ($form->created_by !== Auth::id()) {
            abort(401, 'Nincs jogosultságod törölni ezt a formot!');
        }

        $hasAnswers = false;
        $questions = $form->questions;
        foreach ($questions as $question) {
            $answers = $question->answers;
            if (count($answers) > 0) {
                $hasAnswers = true;
                break;
            }
        }

        if ($hasAnswers) {
            abort(403, 'Nem lehet törölni ezt a formot, mert már kitöltötték!');
        }
        $title = $form->title;
        foreach ($form->questions as $question) {
            $question->choices()->delete();
        }
        $form->questions()->delete();
        $deleted = $form->delete();
        if (!$deleted) return abort(500, 'Hiba történt a törlés során!');


        return redirect()->route('forms.home')->with('form-deleted', $title);
    }

    public function restore(Request $request, $id)
    {
        $form = Form::withTrashed()->findOrFail($id);
        if ($form->created_by !== Auth::id()) {
            abort(401, 'Nincs jogosultságod visszaállítani ezt a formot!');
        }


        $form->restore();
        $form->questions()->restore();
        $form->questions()->each(function ($question) {
            $question->choices()->restore();
        });




        return redirect()->route('forms.home',)->with('form-restored', $form->title);
    }
}
