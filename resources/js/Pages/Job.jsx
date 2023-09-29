import { useEffect, useState } from 'react';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { Button, Layout, message, Steps } from 'antd';
import {
  FileUploadForm,
  FinalForm,
  LoadingForm,
} from '@/Components/NewJobSteps/index.jsx';
import { useTranslation } from 'react-i18next';

const { Content } = Layout;

export default function Job({ auth }) {
  const { t } = useTranslation();
  const [job, setJob] = useState(null);
  const [current, setCurrent] = useState(0);
  const [loading, setLoading] = useState(true);
  const [token, setToken] = useState('');

  const onFormChange = () => {
    setCurrent(current + 1);
  };

  const handleSolve = async () => {
    try {
      await axios.post(`/api/job/${job.id}/solve?_token=${token}`);
    } catch (error) {
      message.error(`HandleSolve error: ${error.message}`, 5);
    } finally {
      setCurrent(current + 1);
    }
  };

  const ReusableButtons = () => {
    return (
      <div className="my-2">
        {current < 3 ? (
          <Button htmlType="submit">Continue</Button>
        ) : (
          <Button htmlType="submit">Download</Button>
        )}
      </div>
    );
  };

  const forms = [
    <FileUploadForm onFinish={onFormChange} newJob={job} token={token}>
      <ReusableButtons />
    </FileUploadForm>,
    <LoadingForm onFinish={handleSolve} />,
    <FinalForm>
      <ReusableButtons />
    </FinalForm>,
  ];

  const fetchJob = async () => {
    const pathname = window.location.pathname;
    const id = pathname.split('/').pop();

    try {
      const jobResponse = await axios.get(`/api/job/${id}`);
      setJob(jobResponse.data);
    } catch (error) {
      console.error(error);
    } finally {
      setLoading(false);
    }
  };

  const fetchToken = async () => {
    try {
      const tokenResponse = await axios.get('/login');
      setToken(tokenResponse.data);
    } catch (error) {
      message.error(`Login error: ${error.message}`, 5);
    }
  };

  useEffect(() => {
    fetchToken();
    fetchJob();
  }, []);

  return (
    <AuthenticatedLayout
      user={auth.user}
      header={
        <h2 className="font-semibold text-xl text-gray-800 leading-tight">
          Profile
        </h2>
      }
    >
      <Head title="View Job" />
      {loading ? (
        <Content
          style={{
            textAlign: 'center',
            minHeight: 'calc(100vh - 128px)',
            lineHeight: 4,
          }}
        >
          <div>Loading...</div>
        </Content>
      ) : job ? (
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
                      title={t('upload')}
                      description={t('step.uploadFile')}
                      disabled={current !== 0}
                    />
                    <Steps.Step
                      title={t('step.execution')}
                      description={t('step.solve')}
                      disabled={current !== 1}
                    />
                    <Steps.Step
                      title={t('step.success')}
                      description={t('step.solutionReady')}
                      disabled={current !== 2}
                    />
                  </Steps>
                  {forms[current]}
                </div>
              </div>
            </div>
          </div>
        </Content>
      ) : (
        <Content
          style={{
            textAlign: 'center',
            minHeight: 'calc(100vh - 128px)',
            lineHeight: 4,
          }}
        >
          <div>Job does not exist</div>
        </Content>
      )}
    </AuthenticatedLayout>
  );
}
