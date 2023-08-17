import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { Button, Form, Input, Layout, Steps, Upload, List, Spin } from "antd";
import { UserOutlined } from '@ant-design/icons';
import { useState, useEffect } from "react";
import { SolverwForm, NamingForm, FileUploadForm, LoadingForm, FinalForm } from "@/Components/NewJobSteps";
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
            {current < 4 ? <Button htmlType="submit">Continue</Button> : <Button htmlType="submit">Download</Button> }
        </div>
    }

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