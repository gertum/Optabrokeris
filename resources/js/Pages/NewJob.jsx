import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { Button, Form, Input, Layout, Steps, Upload, List, Spin } from "antd";
import { UserOutlined } from '@ant-design/icons';
import { useState, useEffect } from "react";
import SolverwForm from "@/Components/SolverForm";
import { useTranslation } from 'react-i18next';

const { Content } = Layout;

export default function NewJob({ auth }) {
    const { t } = useTranslation();
    const [current, setCurrent] = useState(0);
    const [name, setName] = useState({});

    const onFinishNamingForm = (values) => {
        setName((prev) => ({ ...prev, ...values }));
        current < 4 && setCurrent(current + 1);
    };

    const onChange = (value) => {
        console.log('onChange:', value);
        setCurrent(value);
    };

    const ReusableButtons = () => {
        return <div className="my-2">
            {/*{current > 0 && <Button htmlType="button" onClick={() => setCurrent(current-1)}>Back</Button>}*/}
            {current < 4 ? <Button htmlType="submit">Continue</Button> : <Button htmlType="submit">Download</Button> }
        </div>
    }

    const NamingForm = ({onFinish}) => {
        return <div className="my-2">
            <Form onFinish={onFinish}>
                <Form.Item label='Enter name' name='newName' rules={[{
                    required: true, message: 'Please enter a name for the profile'
                }]}>
                    <Input size="small" placeholder='Enter profile name' value={name}/>
                </Form.Item>
                <ReusableButtons />
            </Form>
        </div>
    }

    const FileUploadForm = ({onFinish}) => {
        const [uploadedFile, setUploadedFile] = useState(null);

        // Simulated upload function
        const handleFileUpload = (file) => {
            // Simulate a delay for the upload process
            setTimeout(() => {
                setUploadedFile(file.name);
            }, 1000); // Simulated delay of 1 second
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
                <ReusableButtons />
            </Form>
        </div>
    }

    const LoadingForm = ({ onFinish }) => {
        useEffect(() => {
            const timer = setTimeout(() => {
                onFinish();
            }, 3000);

            return () => {
                clearTimeout(timer);
            };
        }, [onFinish]);

        return (
            <div className="my-2">
                <Form>
                    <Spin tip="Executing...">
                        <div style={{ width: '100%', height: '30vh', display: 'flex', justifyContent: 'center', alignItems: 'center' }}>
                            <span></span>
                        </div>
                    </Spin>
                </Form>
            </div>
        );
    };

    const FinalForm = ({ name }) => {
        return (
            <div className="my-2">
                <h2>Final Summary</h2>
                <List
                    header={<div>Data Collected:</div>}
                    bordered
                    dataSource={Object.entries(name)}
                    renderItem={([key, value]) => (
                        <List.Item>
                            <strong>{key}:</strong> {value}
                        </List.Item>
                    )}
                />
                <Button htmlType="submit">Submit</Button>
            </div>
        );
    };

    const forms = [
        <SolverwForm onFinish={onFinishNamingForm}>
            <ReusableButtons />
        </SolverwForm>,
        <NamingForm onFinish={onFinishNamingForm}>
            <ReusableButtons />
        </NamingForm>,
        <FileUploadForm onFinish={onFinishNamingForm}>
            <ReusableButtons />
        </FileUploadForm>,
        <LoadingForm onFinish={() => setCurrent(current + 1)} />,
        <FinalForm name={name} >
            <ReusableButtons />
        </FinalForm>,
    ]

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">New job</h2>}
        >
            <Head title="New Job" />
             <Content style={{
                textAlign: 'center',
                minHeight: 'calc(100vh - 128px)',
                lineHeight: 4,
            }}>
                 <div className="py-12">
                     <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                         <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                             <div className="p-6 text-gray-900">
                                 <Steps
                                     current={current}
                                     onChange={onChange}
                                 >
                                     <Steps.Step
                                         title={t('step.solver')}
                                         description={t('step.chooseSolver')}
                                         disabled={current !== 0}
                                     />
                                     <Steps.Step
                                         title={t('step.name')}
                                         description={t('step.enterProfileName')}
                                         disabled={current !== 1}
                                     />
                                     <Steps.Step
                                         title={t('step.upload')}
                                         description={t('step.uploadFile')}
                                         disabled={current !== 2}
                                     />
                                     <Steps.Step
                                         title={t('step.execution')}
                                         description={t('step.solving')}
                                         disabled={current !== 3}
                                     />
                                     <Steps.Step
                                         title={t('step.success')}
                                         description={t('step.solutionReady')}
                                         disabled={current !== 4}
                                     />
                                 </Steps>
                                 {forms[current]}
                             </div>
                         </div>
                     </div>
                 </div>
             </Content>
        </AuthenticatedLayout>
    );
}