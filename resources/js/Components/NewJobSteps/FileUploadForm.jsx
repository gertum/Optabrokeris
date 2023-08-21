import { useState } from 'react';
import { Button, Form, Upload } from 'antd';

export const FileUploadForm = ({ newJob, onFinish, children, token }) => {
  const [uploadedFile, setUploadedFile] = useState(null);

  const data = {
    timeslotList: [
      {
        id: 1,
        dayOfWeek: 'MONDAY',
        startTime: '08:31:00',
        endTime: '09:30:00',
      },
      {
        id: 2,
        dayOfWeek: 'MONDAY',
        startTime: '09:30:00',
        endTime: '10:30:00',
      },
      {
        id: 3,
        dayOfWeek: 'MONDAY',
        startTime: '10:30:00',
        endTime: '11:30:00',
      },
      {
        id: 4,
        dayOfWeek: 'MONDAY',
        startTime: '13:30:00',
        endTime: '14:30:00',
      },
      {
        id: 5,
        dayOfWeek: 'MONDAY',
        startTime: '14:30:00',
        endTime: '15:30:00',
      },
      {
        id: 6,
        dayOfWeek: 'TUESDAY',
        startTime: '08:30:00',
        endTime: '09:30:00',
      },
      {
        id: 7,
        dayOfWeek: 'TUESDAY',
        startTime: '09:30:00',
        endTime: '10:30:00',
      },
      {
        id: 8,
        dayOfWeek: 'TUESDAY',
        startTime: '10:30:00',
        endTime: '11:30:00',
      },
      {
        id: 9,
        dayOfWeek: 'TUESDAY',
        startTime: '13:30:00',
        endTime: '14:30:00',
      },
      {
        id: 10,
        dayOfWeek: 'TUESDAY',
        startTime: '14:30:00',
        endTime: '15:30:00',
      },
    ],
    roomList: [
      {
        id: 1,
        name: 'Room A',
      },
      {
        id: 2,
        name: 'Room B',
      },
      {
        id: 3,
        name: 'Room C',
      },
    ],
    lessonList: [
      {
        id: 1,
        subject: 'Math',
        teacher: 'A. Turing',
        studentGroup: '9th grade',
        timeslot: {
          id: 1,
          dayOfWeek: 'MONDAY',
          startTime: '08:30:00',
          endTime: '09:30:00',
        },
        room: {
          id: 1,
          name: 'Room A',
        },
      },
      {
        id: 2,
        subject: 'Math',
        teacher: 'A. Turing',
        studentGroup: '9th grade',
        timeslot: null,
        room: null,
      },
      {
        id: 3,
        subject: 'Physics',
        teacher: 'M. Curie',
        studentGroup: '9th grade',
        timeslot: null,
        room: null,
      },
      {
        id: 4,
        subject: 'Chemistry',
        teacher: 'M. Curie',
        studentGroup: '9th grade',
        timeslot: null,
        room: null,
      },
      {
        id: 5,
        subject: 'Biology',
        teacher: 'C. Darwin',
        studentGroup: '9th grade',
        timeslot: null,
        room: null,
      },
      {
        id: 6,
        subject: 'History',
        teacher: 'I. Jones',
        studentGroup: '9th grade',
        timeslot: null,
        room: null,
      },
      {
        id: 7,
        subject: 'English',
        teacher: 'I. Jones',
        studentGroup: '9th grade',
        timeslot: null,
        room: null,
      },
      {
        id: 8,
        subject: 'English',
        teacher: 'I. Jones',
        studentGroup: '9th grade',
        timeslot: null,
        room: null,
      },
      {
        id: 9,
        subject: 'Spanish',
        teacher: 'P. Cruz',
        studentGroup: '9th grade',
        timeslot: null,
        room: null,
      },
      {
        id: 10,
        subject: 'Spanish',
        teacher: 'P. Cruz',
        studentGroup: '9th grade',
        timeslot: null,
        room: null,
      },
      {
        id: 11,
        subject: 'Math',
        teacher: 'A. Turing',
        studentGroup: '10th grade',
        timeslot: null,
        room: null,
      },
      {
        id: 12,
        subject: 'Math',
        teacher: 'A. Turing',
        studentGroup: '10th grade',
        timeslot: null,
        room: null,
      },
      {
        id: 13,
        subject: 'Math',
        teacher: 'A. Turing',
        studentGroup: '10th grade',
        timeslot: null,
        room: null,
      },
      {
        id: 14,
        subject: 'Physics',
        teacher: 'M. Curie',
        studentGroup: '10th grade',
        timeslot: null,
        room: null,
      },
      {
        id: 15,
        subject: 'Chemistry',
        teacher: 'M. Curie',
        studentGroup: '10th grade',
        timeslot: null,
        room: null,
      },
      {
        id: 16,
        subject: 'French',
        teacher: 'M. Curie',
        studentGroup: '10th grade',
        timeslot: null,
        room: null,
      },
      {
        id: 17,
        subject: 'Geography',
        teacher: 'C. Darwin',
        studentGroup: '10th grade',
        timeslot: null,
        room: null,
      },
      {
        id: 18,
        subject: 'History',
        teacher: 'I. Jones',
        studentGroup: '10th grade',
        timeslot: null,
        room: null,
      },
      {
        id: 19,
        subject: 'English',
        teacher: 'P. Cruz',
        studentGroup: '10th grade',
        timeslot: null,
        room: null,
      },
      {
        id: 20,
        subject: 'Spanish',
        teacher: 'P. Cruz',
        studentGroup: '10th grade',
        timeslot: null,
        room: null,
      },
    ],
    score: '-38init/0hard/0soft',
    solverStatus: 'NOT_SOLVING',
  };

  return (
    <div className="my-2">
      <Form onFinish={() => onFinish({ fileData: data })} className="mt-4">
        <Button
          className="my-2"
          onClick={() => console.log('Downloading solver data example...')}
        >
          Download solver data example
        </Button>
        <Upload.Dragger
          action={`/api/job/${newJob.id}/upload?_token=${token}`}
          maxCount={1}
          // beforeUpload={(file) => {
          //     // handleFileUpload(file); // Simulate file upload
          //     console.log('Uploading')
          //     return false
          // }}
          listType="picture"
          accept=".xlsx"
        >
          Drag files here or
          <br />
          <Button>Upload</Button>
        </Upload.Dragger>
        {children}
      </Form>
    </div>
  );
};
