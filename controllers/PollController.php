<?php


class PollController extends Poll
{

    public function index(){
        $user = $this->getUser();
        $getMyPolls = Poll::where(['user_id' => $user->id])->get();
        include "views/home.php";
    }
    
    public function store(){
        $this->validate([
            'title' => ['required', 'string'],
            'question' => ['required', 'string'],
            'answers' => ['required', 'is_array']
        ]);

        $user = $this->getUser();
        $title = $_POST['title'];
        $question = $_POST['question'];
        $is_active = $_POST['is_active'] ?? 0;
        $insert = Poll::insert(['user_id' => $user->id, 'title' => $title, 'question' => $question, 'is_active' => $is_active]);
        foreach($_POST['answers'] as $answer){
            $text = $answer['answer'];
            $votes = $answer['votes'];
            if($text != "" && $text != null && $votes != "" && $votes != null){
                $answerModel = new Answer;
                $answerModel->insert(['poll_id' => $insert, 'answer' => $text, 'votes' => $votes]);
            }
        }
        return json_encode(['status' => true, 'Data' => 'Poll created succesfully', 'id' => $insert, 'destroy_url' => $this->getUrl('api/poll-destroy')]);
    }

    public function destroy(){
        $this->validate(
            [
                'poll_id' => ['required', 'exists:polls,id']
            ]
        );
        $user = $this->getUser();
        Poll::where(['id' => $_POST['poll_id'], 'user_id' => $user->id])->delete();

        return json_encode(['status' => true, 'data' => "Poll destroyed succesfully"]);
    }

    public function getPoll(){
        $this->validate(
            [
                'poll_id' => ['required', 'exists:polls,id'],
            ], 'GET');

        $user = $this->getUser();
        $get = Poll::where(['user_id' => $user->id, 'id' => $_GET['poll_id']])->first();
        $answers = [];
        if($get != null){
            $answer = new Answer;
            $answers = $answer->where(['poll_id' => $_GET['poll_id']])->get();
        }
        return json_encode(['status' => true, 'data' => $get, 'answers' => $answers]);
    }

    public function destroyAnswer(){
        $this->validate(
            [
                'answer_id' => ['required', 'exists:answers,id'],
            ], 'GET'
        );

        $answer = new Answer;

        $find = $answer->where(['id' => $_GET['answer_id']])->first();
        $user = $this->getUser();
        $poll = Poll::where(['id' => $find['poll_id'], 'user_id' => $user->id])->first();
        if($poll != null){
            $answer->where(['id' => $_GET['answer_id']])->delete();
            return json_encode(['status' => true, 'data' => "Answer removed succesfully"]);
        }
        http_response_code(403);
        return json_encode(['status' => false, 'errors' => "Access denied"]);
    }

    public function updatePoll(){
        $this->validate(
            [
                'poll_id' => ['required', 'exists:polls,id'],
            ]
        );

        $params = [];
        if(isset($_POST['title']) && $_POST['title'] != null && $_POST['title'] != ""){
            $params['title'] = $_POST['title'];
        }
        if(isset($_POST['question']) && $_POST['question'] != null && $_POST['question'] != ""){
            $params['question'] = $_POST['question'];
        }
        if(isset($_POST['is_active']) && $_POST['is_active'] != null && $_POST['is_active'] != ""){
            $params['is_active'] = $_POST['is_active'];
        }
        $user = $this->getUser();
        $getPoll = Poll::where(['id' => $_POST['poll_id'], 'user_id' => $user->id])->first();
        if($getPoll != null){
            Poll::where(['id' => $_POST['poll_id'], 'user_id' => $user->id])->update($params);
            foreach($_POST['answers'] as $key => $answer){
                
                $text = $answer['answer'];
                $votes = $answer['votes'];
                if($text != "" && $text != null && $votes != "" && $votes != null){
                    $answerModel = new Answer;
                    $getAnswer = $answerModel->where(['poll_id' => $_POST['poll_id'], 'id' => $key])->first();
        
                    if($getAnswer != null){
                        $answerModel->where(['poll_id' => $_POST['poll_id'], 'id' => $key])->update(['answer' => $text, 'votes' => $votes]);
                    } else {
                        $answerModel->insert(['poll_id' => $_POST['poll_id'], 'answer' => $text, 'votes' => $votes]);
                    }
                }
            }
        }
        return json_encode(['status' => true, 'data' => 'Poll updated succesfully']);   
    }

    public function randomPoll(){
        $user = $this->getUser();
        $getRandom = Poll::where(['user_id' => $user->id])->random();
        $answer = new Answer;
        $getAnswers = $answer->where(['poll_id' => $getRandom['id'], 'is_acrive' => 1])->get();
        return json_encode(['status' => true, 'poll' => $getRandom, 'answers' => $getAnswers]);
    }

}