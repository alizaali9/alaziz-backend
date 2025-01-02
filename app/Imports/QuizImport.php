<?php

namespace App\Imports;

use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuizImport implements  ToCollection, WithHeadingRow
{
    protected $quizData;

    public function __construct(array $quizData)
    {
        $this->quizData = $quizData;
    }

    public function collection(Collection $rows)
    {
        // dd($this->quizData);
        $quiz = Quiz::create($this->quizData);


        foreach ($rows as $row) {
            try {
                Question::create([
                    'quiz_id' => $quiz->id,
                    'question' => $row['question'],
                    'option_a' => $row['optiona'],
                    'option_b' => $row['optionb'],
                    'option_c' => $row['optionc'],
                    'option_d' => $row['optiond'],
                    'answer' => $row['answer'],
                ]);
            } catch (\Exception $e) {
                Log::info($row); 
                Log::info($e->getMessage()); 
            }
        }
        
    }
}
