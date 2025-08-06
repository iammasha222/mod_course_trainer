<?php

defined('MOODLE_INTERNAL') || die();

class block_course_trainer_renderer extends plugin_renderer_base {

    public function render_trainer_card(array $users, $currentcourseid){
        $renderdata = ['users' => []];

        foreach ($users as $user) {
            $userpic = $this->output->user_picture($user, ['size' => 35]);
            $username = fullname($user);
            $useremail = s($user->email);

            $othercourses = enrol_get_users_courses($user->id, true, '*', 'visible DESC, sortorder ASC');
            $count = 0;
            $trainercourses = [];

            foreach ($othercourses as $course) {
                if ($course->id == $currentcourseid) {
                    continue;
                }
                $context = context_course::instance($course->id);
                $roles = get_user_roles($context, $user->id, false);

                foreach ($roles as $role) {
                    if ($role->shortname == 'teacher') {
                        $trainercourses[] = [
                            'fullname' => format_string($course->fullname),
                            'url' => (new moodle_url('/course/view.php', ['id'=>$course->id]))->out()
                        ];
                        $count++;
                        break;
                    }
                }
            }

            $renderdata['users'][] = [
                'userpic' => $userpic,
                'username' => $username,
                'useremail' => $useremail,
                'usermail' => $useremail,
                'othercoursescount' => $count,
                'courses' => $trainercourses,
            ];
        }

        return $this->render_from_template('block_course_trainer/block_course_trainer', $renderdata);
    }
}
