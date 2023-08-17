import {Form, Input} from "antd";

export const NamingForm = ({onFinish, children}) => {
    return <div className="my-2">
        <Form onFinish={onFinish}>
            <Form.Item label='Enter name' name='newName' rules={[{
                required: true, message: 'Please enter a name for the profile'
            }]}>
                <Input size="small" placeholder='Enter profile name' value={name}/>
            </Form.Item>
            {children}
        </Form>
    </div>
}