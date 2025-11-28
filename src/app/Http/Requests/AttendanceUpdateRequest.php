<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Carbon\Carbon;

class AttendanceUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'clock_in_time'  => ['required', 'date_format:H:i'],
            'clock_out_time' => ['required', 'date_format:H:i'],
            'note' => ['required', 'string'],
        ];

         // breaks 配列に対するルールを動的に作成
        if ($this->has('breaks')) {
            foreach ($this->input('breaks') as $index => $break) {
                $rules["breaks.$index.start"] = ['nullable', 'date_format:H:i'];
                $rules["breaks.$index.end"]   = ['nullable', 'date_format:H:i'];
            }
        }

        return $rules;

    }

    public function messages(): array
    {
        return [
            'clock_in_time.required' => '出勤時間もしくは退勤時間が不適切な値です',
            'clock_out_time.required' => '出勤時間もしくは退勤時間が不適切な値です',
            'note.required' => '備考を記入してください',
            'break_start_time.date_format' => '休憩時間が不適切な値です',
            'break_end_time.date_format' => '休憩時間もしくは退勤時間が不適切な値です',
        ];
    }

     public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $clockIn  = $this->clock_in_time ? Carbon::parse($this->clock_in_time) : null;
            $clockOut = $this->clock_out_time ? Carbon::parse($this->clock_out_time) : null;

            // 1. 出勤と退勤の整合性
            if ($clockIn && $clockOut && $clockIn->gt($clockOut)) {
                $validator->errors()->add('clock_in_time', '出勤時間もしくは退勤時間が不適切な値です');
            }

            // 2. 休憩時間の整合性
             if ($this->has('breaks')) {
                foreach ($this->input('breaks') as $index => $break) {
                    $breakStart = $break['start'] ? Carbon::parse($break['start']) : null;
                    $breakEnd   = $break['end'] ? Carbon::parse($break['end']) : null;

                    if ($breakStart && $clockIn && $breakStart->lt($clockIn)) {
                        $validator->errors()->add("breaks.$index.start", '休憩時間が不適切な値です');
                    }
                    if ($breakStart && $clockOut && $breakStart->gt($clockOut)) {
                        $validator->errors()->add("breaks.$index.start", '休憩時間が不適切な値です');
                    }
                    if ($breakEnd && $clockOut && $breakEnd->gt($clockOut)) {
                        $validator->errors()->add("breaks.$index.end", '休憩時間もしくは退勤時間が不適切な値です');
                    }
                }
            }
        });
    }
}
