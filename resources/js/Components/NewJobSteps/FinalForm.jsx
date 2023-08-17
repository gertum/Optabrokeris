import {Button, List} from "antd";

export const FinalForm = ({ name }) => {
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
            <Button htmlType="submit">Back to list</Button>
        </div>
    );
};