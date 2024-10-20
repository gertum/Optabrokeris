import {Button, Divider, Space, Spin} from "antd";
import axios from "axios";
import {DownloadOutlined} from "@ant-design/icons";
import {useEffect, useState} from "react";

export const FinalForm = ({token, job, disabled, children, onSolve, onStop}) => {
    const [timerStarted, setTimerStarted] = useState(false);
    const [solving, setSolving] = useState(false);
    const [timeLeft, setTimeLeft] = useState(20);

    if (!job || !job.flag_uploaded) {
        return null;
    }

    useEffect(() => {
        setSolving(Boolean(job.flag_solving && !job.flag_solved));
    }, []);

    useEffect(() => {
        let timer;
        let countdownInterval;

        if (solving && !timerStarted) {
            setTimerStarted(true);

            // Start countdown interval
            countdownInterval = setInterval(() => {
                setTimeLeft((prevTime) => {
                    if (prevTime > 1) {
                        return prevTime - 1;
                    } else {
                        clearInterval(countdownInterval);
                        handleStop(); // Stop solving when timer reaches zero
                        return 0;
                    }
                });
            }, 1000); // Update every second

            // Set timer to automatically stop solving after 20 seconds
            timer = setTimeout(() => {
                handleStop();
            }, 20000);
        }

        return () => {
            if (timer) clearTimeout(timer); // Clear the timer on unmount
            if (countdownInterval) clearInterval(countdownInterval); // Clear the interval on unmount
        };
    }, [solving]);

    const handleSolve = async () => {
        const response = await axios.post(`/api/job/${job.id}/solve?_token=${token}`);

        onSolve();
        setSolving(true);
        setTimeLeft(20);

        return response.data;
    };

    const handleStop = async () => {
        const response = await axios.post(`/api/job/${job.id}/stop?_token=${token}`);

        onStop();
        setSolving(false);
        setTimerStarted(false);

        return response.data;
    };

    return (
        <>
            <Divider orientation="left">Solution</Divider>
            <div className="my-2">
                <Space>
                    {solving && (
                        <Button size="large" danger onClick={handleStop}>
                            Stop solving
                        </Button>
                    )}
                    {!solving && (
                        <Button size="large" onClick={handleSolve}>
                            {job.flag_solved ? 'Start re-solving' : 'Start solving'}
                        </Button>
                    )}
                </Space>
            </div>
            {!!job.flag_solving && !job.flag_solved && (
                <div>
                    <Spin size="large" />
                    {timeLeft > 0 && (
                        <div className="text-red-500">{`Will resolve in ${timeLeft} seconds.`}</div>
                    )}
                </div>
            )}
            {!!(job && ((job.flag_solving) || job.flag_solved)) && (
                <Button
                    type="primary"
                    shape="round"
                    icon={<DownloadOutlined />}
                    size="large"
                    href={`/api/job/${job.id}/download?_token=${token}`}
                    target="_blank"
                >
                    {!!job.flag_solving && !job.flag_solved ? 'Download intermediate result' : 'Download result'}
                </Button>
            )}
            {children}
        </>
    );
};
