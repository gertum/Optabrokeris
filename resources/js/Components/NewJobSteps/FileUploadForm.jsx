import {useState} from "react";
import {Button, Form, Upload} from "antd";

export const FileUploadForm = ({onFinish, children}) => {
    const [uploadedFile, setUploadedFile] = useState(null);

    const handleFileUpload = (file) => {
        setTimeout(() => {
            setUploadedFile(file.name);
        }, 1000);
    };

    return <div className="my-2">
        <Form onFinish={() => onFinish({uploadedFile})} className="mt-4">
            <Button className="my-2" onClick={() => console.log('Downloading solver data example...')}>
                Download solver data example
            </Button>
            <Upload.Dragger
                // multiple
                // action={"http://localhost:3000/upload/test"}
                maxCount={1}
                beforeUpload={(file) => {
                    handleFileUpload(file); // Simulate file upload
                    return false; // Prevent default upload behavior
                }}
                listType="picture"
                accept='.xls, .xlsx, .json'
                // beforeUpload={(file) => {
                //     console.log({file})                     check if the files size is to big and so on}
                //     return file or return false
                // }
            >
                Drag files here or
                <br />
                <Button>Upload</Button>
            </Upload.Dragger>
            {children}
        </Form>
    </div>
}