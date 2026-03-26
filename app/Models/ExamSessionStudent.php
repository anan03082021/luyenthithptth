<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ExamSessionStudent extends Model
{
    protected $fillable = ['exam_session_id', 'user_id', 'student_email', 'student_name'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function session()
    {
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
    }
}