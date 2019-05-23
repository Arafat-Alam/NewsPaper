<?php

namespace App\Http\Controllers\BackEnd;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Services\BackEnd\OnlinePollService;

class OnlinePollController extends Controller
{
    public function onlineQuestionPoll()
    {
        $pollQuestions = OnlinePollService::getPollQuestion();
        return view('backend.onlinePoll.onlinePoll',compact('pollQuestions'));
    }

    public function saveOnlinePollQuestion(Request $request)
    {
        $status = OnlinePollService::saveOnlinePollQuestion($request->all());
        if ($status === true) {
            return redirect()->route('onlineQuestionPoll')->with('success', 'Online Poll Question Save Successfull.');
        } else {
            return redirect()->route('onlineQuestionPoll')->with('error', $status);
        }
    }

    public function pollQuestionEditModal($questuinId = null){
        $question = OnlinePollService::getOnlinePollQuestionById($questuinId);
        return view('backend.onlinePoll.pollQuestionEditModal',compact('question'));
    }

    public function saveEditPollQuestion(Request $request)
    {
        $editQuestion = OnlinePollService::saveEditPollQuestion($request->all());
        if ($editQuestion === true) {
            return redirect()->route('onlineQuestionPoll')->with('success', 'Online Poll Question Update Successfull.');
        } else {
            return redirect()->route('onlineQuestionPoll')->with('error', $editQuestion);
        }
    }

    public function inactivePollQUestion($id = null)
    {
        $inactiveDivision = OnlinePollService::inactivePollQUestion($id);
        if ($inactiveDivision === true) {
            return response()->json(['success' => true, 'status' => "Inactive Successfull."]);
        } else {
            return response()->json(['error' => true, 'status' => $inactiveDivision]);
        }
    }

    public function activePollQUestion($id = null)
    {
        $activeDivision = OnlinePollService::activePollQUestion($id);
        if ($activeDivision === true) {
            return response()->json(['success' => true, 'status' => "Active Successfull."]);
        } else {
            return response()->json(['error' => true, 'status' => $activeDivision]);
        }
    }
}
