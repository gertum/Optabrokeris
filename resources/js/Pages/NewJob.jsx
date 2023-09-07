import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { Button, Layout, message, Steps } from 'antd';
import { useState, useEffect } from 'react';
import {
  SolverwForm,
  NamingForm,
  FileUploadForm,
  LoadingForm,
  FinalForm,
} from '@/Components/NewJobSteps';
import { useTranslation } from 'react-i18next';
import axios from 'axios';

const { Content } = Layout;

export default function NewJob({ auth }) {
  const { t } = useTranslation();
  const [current, setCurrent] = useState(0);
  const [newJob, setNewJob] = useState({});
  const [token, setToken] = useState('');

  const onFormChange = values => {
    setNewJob(prev => ({ ...prev, ...values }));
    setCurrent(current + 1);
  };

  const onNameSubmit = values => {
    setNewJob(prev => ({ ...prev, ...values }));
    const requestData = {
      type: newJob.type,
      // ...values
    };
    axios
      .post(`/api/job?_token=${token}`, requestData)
      .then(response => {
        setNewJob(prev => ({ ...prev, id: response.data.id }));
      })
      .catch(error => {
        message.error(`Name submit error: ${error.message}`, 5);
      });
    setCurrent(current + 1);
  };

  const handleSolve = () => {
    axios
      .post(`/api/job/${newJob.id}/solve?_token=${token}`)
      .then(response => {
        console.log('handleSolve: ', response.data);
      })
      .catch(error => {
        message.error(`HandleSolve error: ${error.message}`, 5);
      });
    setCurrent(current + 1);
  };

  const onFileUploadFinish = () => {
    setCurrent(current + 1);
  };

  const ReusableButtons = () => {
    return (
      <div className="my-2">
        {current < 4 ? (
          <Button htmlType="submit">Continue</Button>
        ) : (
          <Button htmlType="submit">Download</Button>
        )}
      </div>
    );
  };

  const forms = [
    <SolverwForm onFinish={onFormChange}>
      <ReusableButtons />
    </SolverwForm>,
    <NamingForm onFinish={onNameSubmit}>
      <ReusableButtons />
    </NamingForm>,
    <FileUploadForm onFinish={onFileUploadFinish} newJob={newJob} token={token}>
      <ReusableButtons />
    </FileUploadForm>,
    <LoadingForm onFinish={handleSolve} />,
    <FinalForm>
      <ReusableButtons />
    </FinalForm>,
  ];

  useEffect(() => {
    axios
      .get('/login')
      .then(response => {
        setToken(response.data);
      })
      .catch(error => {
        message.error(`Login error: ${error.message}`, 5);
      });
  }, []);

  return (
    <AuthenticatedLayout
      user={auth.user}
      header={
        <h2 className="font-semibold text-xl text-gray-800 leading-tight">
          New job
        </h2>
      }
    >
      <Head title="New Job" />
      <Content
        style={{
          textAlign: 'center',
          minHeight: 'calc(100vh - 128px)',
          lineHeight: 4,
        }}
      >
        <div className="py-12">
          <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div className="p-6 text-gray-900">
                <Steps current={current}>
                  <Steps.Step
                    title={t('step.solver')}
                    description={t('step.chooseSolver')}
                    disabled={current !== 0}
                  />
                  <Steps.Step
                    title={t('step.name')}
                    description={t('step.enterJobName')}
                    disabled={current !== 1}
                  />
                  <Steps.Step
                    title={t('upload')}
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
