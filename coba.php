<?php

function login() {
    $username = $this->security->xss_clean($this->input->post('username'));
    $password = $this->security->xss_clean($this->input->post('password'));

    $this->db->where('userName', $username);
    $this->db->where('password', $password);

    $q = $this->db->query("select * from userDetail, prvUser where userDetail.userID = prvUser.userID AND userName like '" . $username . "'");

    if ($query->num_rows == 1) {
        $row = $query->row();
        $data = array(
            'userID' => $row->userID,
            'session_id' => $row->userID,
            'userName' => $row->userName,
            'CD' => "$row->CreateDashboard",
            'ED' => $row->EditDashboard,
            'DD' => $row->DeleteDashboard,
            'CP' => $row->CreatePanel,
            'EP' => $row->EditPanel,
            'DP' => $row->DeletePanel,
            'validated' => true
        );
        $this->session->set_userdata($data);
        return true;
    }
    return false;
}
    ?>