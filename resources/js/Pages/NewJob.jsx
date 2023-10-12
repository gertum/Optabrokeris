import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import {Button, Divider, Layout, message, Space, Spin, Steps} from 'antd';
import { useState, useEffect } from 'react';
import {
  SolverForm,
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
  const [job, setJob] = useState({});
  const [token, setToken] = useState('');
  const [file, setFile] = useState(null);
  const [solver, setSolver] = useState(null);
  const [solveInterval, setSolveInterval] = useState(null);

  const onFormChange = values => {
    setJob({ ...job, ...values });
    setCurrent(current + 1);
  };

  const onNameSubmit = async values => {
    try {
      const requestData = {
        type: job.type,
        ...values,
      };

      await axios.post(
        `/api/job?_token=${token}`,
        requestData
      ).then(response => {
        setJob(response.data);
        setCurrent(current + 1);
      });
    } catch (error) {
      message.error(`Name submit error: ${error.message}`, 5);
    }
  };

  const handleSolve = async () => {
    const response = await axios.post(`/api/job/${job.id}/solve?_token=${token}`);

    return response.data;
  };

  useEffect(() => {
    if (current === 3 && job !== null && job.id !== undefined) {
      handleSolve()
          .then(() => {
            const solveInterval = setInterval(async () => {
              await axios
                  .get(`/api/job/${job.id}?_token=${token}`)
                  .then((response) => {
                    console.log(response.data.flag_solved);
                    if (response.data.flag_solved) {
                      setJob(response.data);
                      clearInterval(solveInterval);
                    }
                  });
            }, 5000);
          })
          .catch((error) => {
            message.error(`HandleSolve error: ${error.message}`, 5);
          });
    }
  }, [current, job.id]);

  useEffect(() => {
    if (job.flag_solved) {
      setCurrent(current + 1);
      window.open(`/api/job/${job.id}/download?_token=${token}`, '_blank');
    }
  }, [job.flag_solved]);

  const handleSolverSelect = (index) => {
    setSolver(index);
  };

  const onFileUploadFinish = () => {
    setCurrent(current + 1);
  };

  const ReusableButtons = (props) => {
    return (
      <>
        <Divider dashed />
        <Space wrap>
          <Button onClick={() => setCurrent(current - (current === 4 ? 2 : 1))}>Step back</Button>
          {current < 4 && <Button htmlType="submit" type={'primary'} disabled={props.continueDisabled}>Continue</Button>}
        </Space>
      </>
    );
  };

  const forms = [
    <SolverForm onFinish={onFormChange} onSelect={handleSolverSelect}>
      <ReusableButtons continueDisabled={solver === null} />
    </SolverForm>,
    <NamingForm onFinish={onNameSubmit}>
      <ReusableButtons />
    </NamingForm>,
    <FileUploadForm onFinish={onFileUploadFinish} onUploadChange={(file) => setFile(file)} newJob={job} token={token}>
      <ReusableButtons continueDisabled={file === null} />
    </FileUploadForm>,
    <LoadingForm>
      <p>{`Please wait for solution to be ready, this may take up to a minute`}</p>
      <Spin tip="Solving..." size={'large'}></Spin>
    </LoadingForm>,
    <FinalForm token={token} jobId={job.id} created>
      <ReusableButtons />
    </FinalForm>,
  ];

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
          <div className="mx-auto sm:px-6 lg:px-8">
            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div className="p-6 text-gray-900">
                <Steps current={current}>
                  <Steps.Step
                    title={t('step.solver')}
                    description={
                      job?.type
                        ? `${t('step.chosenSolver')}: ${t(
                            `step.solverForm.${job.type}`
                          )}`
                        : t('step.chooseSolver')
                    }
                    disabled={current !== 0}
                  />
                  <Steps.Step
                    title={t('step.name')}
                    description={
                      job?.name
                        ? `${t('step.enteredName')}: ${job.name}`
                        : t('step.enterJobName')
                    }
                    disabled={current !== 1}
                  />
                  <Steps.Step
                    title={t('upload')}
                    description={t('step.uploadFile')}
                    disabled={current < 2}
                  />
                  <Steps.Step
                    title={t('step.execution')}
                    description={t('step.solve')}
                    disabled={current < 2}
                  />
                  <Steps.Step
                    title={t('step.success')}
                    description={t('step.solutionReady')}
                    disabled={current < 2}
                  />
                </Steps>
                <Divider dashed />
                {forms[current]}
              </div>
            </div>
          </div>
        </div>
      </Content>
    </AuthenticatedLayout>
  );
}
