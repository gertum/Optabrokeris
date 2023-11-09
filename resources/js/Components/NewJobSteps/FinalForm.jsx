import {Button, Divider, Space, Spin} from "antd";
import axios from "axios";

export const FinalForm = ({token, job, disabled, children}) => {
    if (!job) {
        return;
    }

    const handleSolve = async () => {
        const response = await axios.post(`/api/job/${job.id}/solve?_token=${token}`);

        return response.data;
    };

    const handleStop = async () => {
        const response = await axios.post(`/api/job/${job.id}/stop?_token=${token}`);

        return response.data;
    };

    return <>
        <Divider orientation="left">Solution</Divider>
        <div className="my-2">
            <Space>
                <Button size="large" danger onClick={handleStop}>
                    Stop solving
                </Button>
                <Button size="large" type="primary" onClick={handleSolve}>
                    {job.flag_solving ? 'Restart solving' : 'Start solving'}
                </Button>
            </Space>
        </div>
        {job.flag_solving && <div><Spin tip="Solving" size="large" /></div>}
        {job && <div><a href={`/api/job/${job.id}/download?_token=${token}`}>{job.flag_solving ? 'Download intermediate result' : 'Download result' }</a></div>}
        {children}
        <Divider />
    </>;
};
