import {Button, Divider, Space, Spin} from "antd";
import axios from "axios";
import {DownloadOutlined} from "@ant-design/icons";
import {useState} from "react";

export const FinalForm = ({token, job, disabled, children, onSolve, onStop}) => {
    const [wasStarted, setWasStarted] = useState(false);

    if (!job) {
        return;
    }

    const handleSolve = async () => {
        const response = await axios.post(`/api/job/${job.id}/solve?_token=${token}`);

        onSolve();
        setWasStarted(true);

        return response.data;
    };

    const handleStop = async () => {
        const response = await axios.post(`/api/job/${job.id}/stop?_token=${token}`);

        onStop();

        return response.data;
    };

    return <>
        <Divider orientation="left">Solution</Divider>
        <div className="my-2">
            <Space>
                {!!job.flag_solving && <Button size="large" danger onClick={handleStop}>Stop solving</Button>}
                {!job.flag_solving && <Button size="large" onClick={handleSolve}>Start solving</Button>}
            </Space>
        </div>
        {!!job.flag_solving && <div><Spin tip="Solving" size="large" /></div>}
        {
            !!(job && (job.flag_solving || job.flag_solved || wasStarted))
            && <Button
                type="primary"
                shape="round"
                icon={<DownloadOutlined />}
                size="large"
                href={`/api/job/${job.id}/download?_token=${token}`}
                target="_blank"
            >
                {!!job.flag_solving ? 'Download intermediate result' : 'Download result' }
            </Button>
        }
        {children}
        <Divider />
    </>;
};
