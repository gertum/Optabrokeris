import { useEffect, useState } from 'react';
import { Form, Spin } from 'antd';
import axios from 'axios';

export const LoadingForm = ({ newJob, onFinish, children }) => {
  const [loading, setLoading] = useState(null);
  const [error, setError] = useState(null);
  useEffect(() => {
    // if (loading === null) handleNewJob()
    if (loading === null) setLoading(false);
    if (loading === false) onFinish();
    // if (loading) {
    //     const timer = setTimeout(() => {
    //         onFinish();
    //     }, 3000);
    //
    //     return () => {
    //         clearTimeout(timer);
    //     };
    // }
    // if (loading === false && !error) {
    //     const timer = setTimeout(() => {
    //         onFinish();
    //     }, 3000);
    //
    //     return () => {
    //         clearTimeout(timer);
    //     };
    // }
    // if(loading === false && error) {
    //     onFinish()
    // }
  }, [loading]);

  const queryParams = {
    // name: newJob.name,
    type: newJob.type,
  };

  const handleNewJob = async () => {
    try {
      setLoading(true);
      const response = await axios.post('/api/job', newJob.data, {
        headers: {
          'Content-Type': 'application/json',
          // Add any other headers if needed
        },
        params: queryParams, // Pass query parameters here
      });

      console.log('Success:', response.data);
    } catch (error) {
      setError(error.message);
      console.error('Error:', error);
    } finally {
      setLoading(false);
      // Code here will run regardless of success or error
    }
  };

  // fetch('/api/job?type=school', {
  //     method: 'POST',
  //     headers: {
  //         'Content-Type': 'application/json',
  //         // Add any other headers if needed
  //     },
  //     body: JSON.stringify(data),
  // })
  //     .then(response => response.json())
  //     .then(result => {
  //         console.log('Success:', result);
  //     })
  //     .catch(error => {
  //         console.error('Error:', error);
  //     });

  return (
    <div className="my-2">
      <Form>
        <Spin tip="Executing...">
          <div
            style={{
              width: '100%',
              height: '30vh',
              display: 'flex',
              justifyContent: 'center',
              alignItems: 'center',
            }}
          >
            <span></span>
          </div>
        </Spin>
      </Form>
    </div>
  );
};
