<?php
/**
 * Created by PhpStorm.
 * User: aaronluman
 * Date: 4/10/15
 * Time: 1:27 PM
 */

class PingBackTest extends PHPUnit_Framework_TestCase
{
    public function providerForTestValidateIPAddressIsInWhiteList()
    {
        return [
            [
                '{"parameters":{},"ipAddress":"174.36.92.186, 192.168.56.10"}',
                '',
                TRUE,
            ],
            [
                '{"parameters":{},"ipAddress":"174.36.92.186"}',
                '',
                TRUE,
            ],
            [
                '{"parameters":{},"ipAddress":"192.168.56.10"}',
                '',
                FALSE,
            ],
            [
                '{"parameters":{},"ipAddress":"192.168.56.10"}',
                '192.168.56.10',
                TRUE,
            ],
        ];
    }

    /**
     * @dataProvider providerForTestValidateIPAddressIsInWhiteList
     */
    public function testValidateIPAddressIsInWhiteList($dataString, $additionalIP, $success)
    {
        $data = json_decode($dataString, TRUE);
        $callback = new Paymentwall_Pingback($data['parameters'], $data['ipAddress']);
        if (!empty($additionalIP)) {
            $callback->addToWhiteList($additionalIP);
        }
        $this->assertEquals($success, $callback->isIpAddressValid());
    }

    public function providerForTestIsDeliverable()
    {
        return [
            [
                '{"parameters":{"type":"0"},"ipAddress":""}',
                [],
                TRUE
            ],
            [
                '{"parameters":{"type":"0"},"ipAddress":""}',
                [
                    Paymentwall_Pingback::PINGBACK_TYPE_GOODWILL,
                    Paymentwall_Pingback::PINGBACK_TYPE_RISK_REVIEWED_ACCEPTED,
                ],
                FALSE
            ],
            [
                '{"parameters":{"type":"200"},"ipAddress":""}',
                [],
                FALSE
            ],
            [
                '{"parameters":{"type":"1"},"ipAddress":""}',
                [],
                TRUE
            ],
            [
                '{"parameters":{"type":"1"},"ipAddress":""}',
                [
                    Paymentwall_Pingback::PINGBACK_TYPE_REGULAR,
                    Paymentwall_Pingback::PINGBACK_TYPE_RISK_REVIEWED_ACCEPTED,
                ],
                FALSE
            ],
            [
                '{"parameters":{"type":"2"},"ipAddress":""}',
                [],
                FALSE
            ],
        ];
    }

    /**
     * @dataProvider providerForTestIsDeliverable
     */
    public function testIsDeliverable($dataString, $deliverableList, $success)
    {
        $data = json_decode($dataString, TRUE);
        $callback = new Paymentwall_Pingback($data['parameters'], $data['ipAddress']);
        if (!empty($deliverableList)) {
            $callback->setDeliverable($deliverableList);
        }
        $this->assertEquals($success, $callback->isDeliverable());
    }
}
