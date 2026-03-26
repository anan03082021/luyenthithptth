<?php

namespace App\Services;

use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\AttemptAnswer;
use Illuminate\Support\Facades\DB;

class ExamService
{
    public function calculateScore($attemptId)
{
    $attempt = ExamAttempt::with('attemptAnswers.question')->find($attemptId);
    if (!$attempt) return 0;

    $totalScore = 0;
    $answers = $attempt->attemptAnswers;

    // 1. Tính điểm trắc nghiệm đơn (Dạng 1)
    $singleQuestions = $answers->filter(function ($ans) {
        // Những câu không có cha thường là câu trắc nghiệm đơn
        return is_null($ans->question->parent_id) && $ans->question->type === 'single_choice';
    });

    foreach ($singleQuestions as $ans) {
        if ($ans->is_correct) $totalScore += 0.25;
    }

    // 2. Tính điểm Đúng/Sai (Dạng 2)
    // CỨ CÓ PARENT_ID THÌ GOM NHÓM LẠI
    $groupQuestions = $answers->filter(function ($ans) {
        return !is_null($ans->question->parent_id);
    })->groupBy('question.parent_id');

    foreach ($groupQuestions as $parentId => $groupAnswers) {
        // Đếm số ý đúng trong chùm 4 ý
        $correctCount = $groupAnswers->where('is_correct', 1)->count();
        
        switch ($correctCount) {
            case 1: $totalScore += 0.10; break;
            case 2: $totalScore += 0.25; break;
            case 3: $totalScore += 0.50; break;
            case 4: $totalScore += 1.00; break;
        }
    }

    $totalScore = round($totalScore, 2);
    $attempt->update(['total_score' => $totalScore]);
    
    return $totalScore;
}
    public function getReviewSuggestions($attemptId)
    {
        $weakTopics = DB::table('attempt_answers')
            ->join('questions', 'attempt_answers.question_id', '=', 'questions.id')
            ->join('topics', 'questions.topic_id', '=', 'topics.id')
            ->where('attempt_answers.attempt_id', $attemptId)
            ->where('attempt_answers.is_correct', false)
            ->select('topics.name', DB::raw('count(*) as wrong_count'))
            ->groupBy('topics.id', 'topics.name')
            ->orderByDesc('wrong_count')
            ->get();

        $suggestions = [];
        foreach ($weakTopics as $topic) {
            $suggestions[] = "Bạn đã làm sai {$topic->wrong_count} câu thuộc chủ đề '{$topic->name}'. Hãy ôn tập lại chương này.";
        }

        return empty($suggestions) ? ["Chúc mừng! Bạn đã làm đúng tất cả các câu hỏi."] : $suggestions;
    }
}