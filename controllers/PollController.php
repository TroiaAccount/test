<?php

class PollController extends Helpers
{
    
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
        $insert = $this->conn->prepare("INSERT polls(user_id, title, question, is_active) VALUES (?, ?, ?, ?)");
        $insert->bind_param('issi', $user->id, $title, $question, $is_active);
        $insert->execute();
        $insert_id = $this->conn->insert_id;
        foreach($_POST['answers'] as $answer){
            $text = $answer['answer'];
            $votes = $answer['votes'];
            if($text != "" && $text != null && $votes != "" && $votes != null) $this->conn->query("INSERT answers (poll_id, answer, votes) VALUES ($insert_id, '$text', $votes)");
        }
        return json_encode(['status' => true, 'Data' => 'Poll created succesfully', 'id' => $insert_id, 'destroy_url' => $this->getUrl('api/poll-destroy')]);
    }

    public function destroy(){
        $this->validate(
            [
                'poll_id' => ['required', 'exists:polls,id']
            ]
        );

        $this->conn->query("DELETE FROM polls WHERE id = " . $_POST['poll_id']);
        return json_encode(['status' => true, 'data' => "Poll destroyed succesfully"]);
    }

    public function get(){
        $this->validate(
            [
                'poll_id' => ['required', 'exists:polls,id'],
            ], 'GET');

        $user = $this->getUser();
        $get = $this->conn->query("SELECT * FROM polls WHERE user_id = $user->id AND id = " . $_GET['poll_id'])->fetch_assoc();
        $answers = [];
        if($get != null){
            $answers = $this->conn->prepare("SELECT * FROM answers WHERE poll_id = ?");
            $answers->bind_param("i", $_GET['poll_id']);
            $answers->execute();
            $result = $answers->get_result();
            $answers = $result->fetch_all(MYSQLI_ASSOC);
        }
        return json_encode(['status' => true, 'data' => $get, 'answers' => $answers]);
    }

    public function update(){
        $this->validate(
            [
                'poll_id' => ['required', 'exists:polls,id'],
            ]
        );

        $sql = "UPDATE polls SET ";
        if(isset($_POST['title']) && $_POST['title'] != null && $_POST['title'] != ""){
            $sql .= "title='" . $_POST['title'] . "',";
        }
        if(isset($_POST['question']) && $_POST['question'] != null && $_POST['question'] != ""){
            $sql .= "question='" . $_POST['question'] . "',";
        }
        if(isset($_POST['is_active']) && $_POST['is_active'] != null && $_POST['is_active'] != ""){
            $sql .= "is_active='" . $_POST['is_active'] . "',";
        }

        echo substr($sql, 0, -1);
        exit;
        
    }

}