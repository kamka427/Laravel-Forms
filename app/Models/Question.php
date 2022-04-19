<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'form_id', 'question', 'answer_type', 'required'
    ];

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }

    public function choices()
    {
        return $this->hasMany(Choice::class, 'question_id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, 'question_id');
    }
}
