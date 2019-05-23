<?php
/**
 * Created by PhpStorm.
 * User: developer1
 * Date: 7/27/2016
 * Time: 1:54 PM
 */

namespace App\Services\BackEnd;
use DB;
use Lang;
use Session;


class OnlinePollService
{
    //=======@@  Start Division Section  @@=======

    public static function saveOnlinePollQuestion($data = null)
    {
        try {
            $satus = DB::table('poll_questions')
                ->insert([
                    'question_date'       => $data['question_date'],
                    'poll_question_lang1' => $data['poll_question_lang1'],
                    'poll_question_lang2' => $data['poll_question_lang2'],
                    'yes_vote'            => 0,
                    'no_vote'             => 0,
                    'no_comments'         => 0,
                    'status'              => 0,
                    'created_at'          => date('Y-m-d h:i:s'),
                    'created_by'          => Session::get('admin.id')
                ]);
            if ($satus){
                return true;
            }
        } catch (\Exception $e) {
            $err_msg = \Lang::get("mysqlError." . $e->errorInfo[1]);
            return $err_msg;
        }
    }

    public static function getPollQuestion()
    {
        return DB::table('poll_questions')->get();
    }

    public static function getOnlinePollQuestionById($questuinId = null)
    {
        return DB::table('poll_questions')
            ->where('id',$questuinId)
            ->first();
    }

    public static function saveEditPollQuestion($data = null)
    {
        try {
            DB::table('poll_questions')
                ->where('id', $data['question_id'])
                ->update([
                    'question_date'       => $data['question_date'],
                    'poll_question_lang1' => $data['poll_question_lang1'],
                    'poll_question_lang2' => $data['poll_question_lang2'],
                    'updated_at'          => date('Y-m-d h:i:s'),
                    'updated_by'          => Session::get('admin.id')
                ]);
            return true;

        } catch (\Exception $e) {
            $err_msg = \Lang::get("mysqlError." . $e->errorInfo[1]);
            return $err_msg;
        }
    }

    public static function inactivePollQUestion($id = null)
    {
        try {
            DB::table('poll_questions')
                ->where('id', $id)
                ->update([
                    'status' => 0,
                ]);
            return true;
        } catch (\Exception $e) {
            $err_msg = \Lang::get("mysqlError." . $e->errorInfo[1]);
            return $err_msg;
        }
    }

    public static function activePollQUestion($id = null)
    {
        try {
            DB::table('poll_questions')
                ->where('id', $id)
                ->update([
                    'status' => 1,
                ]);
            return true;
        } catch (\Exception $e) {
            $err_msg = \Lang::get("mysqlError." . $e->errorInfo[1]);
            return $err_msg;
        }
    }

//=======@@ End Division Section  @@=======
}