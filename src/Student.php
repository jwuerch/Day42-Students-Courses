<?php
    class Student  {
        private $name;
        private $enrollment;
        private $id;

        public function __construct($name, $enrollment, $id = null) {
            $this->name = $name;
            $this->enrollment = $enrollment;
            $this->id = $id;
        }

        //Setters;
        public function setName($name) {
            $this->name = $name;
        }

        public function setEnrollment($enrollment) {
            $this->enrollment = $enrollment;
        }

        //Getters;
        public function getName() {
            return $this->name;
        }

        public function getEnrollment() {
            return $this->enrollment;
        }

        public function getId() {
            return $this->id;
        }

        public function save(){
            $GLOBALS['DB']->exec("INSERT INTO students (name, enrollment) VALUES ('{$this->getName()}', '{$this->getEnrollment()}');");
            $this->id = $GLOBALS['DB']->lastInsertId();
        }

        static function getAll(){
            $returned_students = $GLOBALS['DB']->query("SELECT * FROM students;");
            $students = array();

            foreach($returned_students as $student)
            {$name = $student['name'];
             $enrollment = $student['enrollment'];
             $id = $student['id'];
             $new_student = new Student($name, $enrollment, $id);
             array_push($students, $new_student);
            }
            return $students;
         }

         static function deleteAll(){
             $GLOBALS['DB']->exec("DELETE FROM students;");

         }

         static function find($search_id) {
             $students = Student::getAll();
             $found_student = null;
             foreach($students as $student){
                 $student_id = $student->getId();
                 if($student_id == $search_id){
                     $found_student = $student;
                 }
             }
             return $found_student;
         }

         public function delete() {
             $GLOBALS['DB']->exec("DELETE FROM students WHERE id = {$this->getId()};");
         }

         public function update($new_name, $new_enrollment){
             $GLOBALS['DB']->exec("UPDATE students SET name = '{$new_name}', enrollment = '{$new_enrollment}' WHERE id = {$this->getId()};");
             $this->setName($new_name);
             $this->setEnrollment($new_enrollment);
         }

         static function search($search_term) {
             $found_students = array();
             $students = Student::getAll();
             foreach ($students as $student) {
                 $name = $student->getName();
                 similar_text($search_term, $name, $percentage);
                 if ($percentage > 35) {
                     array_push($found_students, $student);
                 }
             }
             return $found_students;
         }

         public function addCourse($course) {
             $GLOBALS['DB']->exec("INSERT INTO courses_students (student_id, course_id) VALUES ({$this->getId()}, {$course->getId()});");
         }

         public function getCourses() {
             $query = $GLOBALS['DB']->query("SELECT course_id FROM courses_students WHERE student_id = {$this->getId()};");
             $course_ids = $query->fetchAll(PDO::FETCH_ASSOC);
             $courses = array();

             foreach ($course_ids as $id) {
                 $course_id = $id['course_id'];
                 $result = $GLOBALS['DB']->query("SELECT * FROM courses WHERE id = {$course_id};");
                 $returned_course = $result->fetchAll(PDO::FETCH_ASSOC);
                 $id = $returned_course[0]['id'];
                 $course_name = $returned_course[0]['course_name'];
                 $course_number = $returned_course[0]['course_number'];
                 $course = new Course($course_name, $course_number, $id);
                 array_push($courses, $course);
             }
             return $courses;
         }
     }
?>
