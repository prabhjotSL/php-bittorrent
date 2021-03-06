<?php
/**
 * PHP BitTorrent
 *
 * Copyright (c) 2011-2012, Christer Edvartsen <cogo@starzinger.net>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * * The above copyright notice and this permission notice shall be included in
 *   all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * @package UnitTests
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011-2012, Christer Edvartsen <cogo@starzinger.net>
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/christeredvartsen/php-bittorrent
 */

namespace PHP\BitTorrent;

/**
 * @package UnitTests
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011-2012, Christer Edvartsen <cogo@starzinger.net>
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/christeredvartsen/php-bittorrent
 */
class DecoderTest extends \PHPUnit_Framework_TestCase {
    /**
     * Decoder instance
     *
     * @var PHP\BitTorrent\Decoder
     */
    private $decoder;

    /**
     * Set up the decoder
     *
     * @covers PHP\BitTorrent\Decoder::__construct
     */
    public function setUp() {
        $this->decoder = new Decoder(new Encoder());
    }

    /**
     * Tear down the decoder
     */
    public function tearDown() {
        $this->decoder = null;
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function getDecodeIntegerData() {
        return array(
            array('i1e', 1),
            array('i-1e', -1),
            array('i0e', 0),
        );
    }

    /**
     * @dataProvider getDecodeIntegerData()
     * @covers PHP\BitTorrent\Decoder::decodeInteger
     */
    public function testDecoderInteger($encoded, $value) {
        $this->assertEquals($value, $this->decoder->decodeInteger($encoded));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function getDecodeInvalidIntegerData() {
        return array(
            array('i01e'),
            array('i-01e'),
            array('ifoobare'),
        );
    }

    /**
     * @dataProvider getDecodeInvalidIntegerData()
     * @expectedException InvalidArgumentException
     * @covers PHP\BitTorrent\Decoder::decodeInteger
     */
    public function testDecodeInvalidInteger($value) {
        $this->decoder->decodeInteger($value);
    }

    /**
     * @expectedException InvalidArgumentException
     * @covers PHP\BitTorrent\Decoder::decodeInteger
     */
    public function testDecodeStringAsInteger() {
        $this->decoder->decodeInteger('4:spam');
    }

    /**
     * @expectedException InvalidArgumentException
     * @covers PHP\BitTorrent\Decoder::decodeInteger
     */
    public function testDecodePartialInteger() {
        $this->decoder->decodeInteger('i10');
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function getDecodeStringData() {
        return array(
            array('4:spam', 'spam'),
            array('11:test string', 'test string'),
            array('3:foobar', 'foo'),
        );
    }

    /**
     * @dataProvider getDecodeStringData()
     * @covers PHP\BitTorrent\Decoder::decodeString
     */
    public function testDecodeString($encoded, $value) {
        $this->assertSame($value, $this->decoder->decodeString($encoded));
    }

    /**
     * @expectedException InvalidArgumentException
     * @covers PHP\BitTorrent\Decoder::decodeString
     */
    public function testDecodeInvalidString() {
        $this->decoder->decodeString('4spam');
    }

    /**
     * @expectedException InvalidArgumentException
     * @covers PHP\BitTorrent\Decoder::decodeString
     */
    public function testDecodeStringWithInvalidLength() {
        $this->decoder->decodeString('6:spam');
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function getDecodeListData() {
        return array(
            array('li1ei2ei3ee', array(1, 2, 3)),
        );
    }

    /**
     * @dataProvider getDecodeListData()
     * @covers PHP\BitTorrent\Decoder::decodeList
     */
    public function testDecodeList($encoded, $value) {
        $this->assertEquals($value, $this->decoder->decodeList($encoded));
    }

    /**
     * @expectedException InvalidArgumentException
     * @covers PHP\BitTorrent\Decoder::decodeList
     */
    public function testDecodeInvalidList() {
        $this->decoder->decodeList('4:spam');
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function getDecodeDictionaryData() {
        return array(
            array('d3:foo3:bar4:spam4:eggse', array('foo' => 'bar', 'spam' => 'eggs')),
        );
    }

    /**
     * @dataProvider getDecodeDictionaryData()
     * @covers PHP\BitTorrent\Decoder::decodeDictionary
     */
    public function testDecodeDictionary($encoded, $value) {
        $this->assertSame($value, $this->decoder->decodeDictionary($encoded));
    }

    /**
     * @expectedException InvalidArgumentException
     * @covers PHP\BitTorrent\Decoder::decodeDictionary
     */
    public function testDecodeInvalidDictionary() {
        $this->decoder->decodeDictionary('4:spam');
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function getGenericDecodeData() {
        return array(
            array('i1e', 1),
            array('4:spam', 'spam'),
            array('li1ei2ei3ee', array(1, 2, 3)),
            array('d3:foo3:bare', array('foo' => 'bar')),
        );
    }

    /**
     * @dataProvider getGenericDecodeData()
     * @covers PHP\BitTorrent\Decoder::decode
     */
    public function testGenericDecode($encoded, $value) {
        $this->assertEquals($value, $this->decoder->decode($encoded));
    }

    /**
     * @expectedException InvalidArgumentException
     * @covers PHP\BitTorrent\Decoder::decode
     */
    public function testGenericDecodeWithInvalidData() {
        $this->decoder->decode('foo');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing "announce" key
     * @covers PHP\BitTorrent\Decoder::decodeFile
     */
    public function testDecodeTorrentFileStrictWithMissingAnnounce() {
        $file = __DIR__ . '/_files/testMissingAnnounce.torrent';
        $this->decoder->decodeFile($file, true);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing "info" key
     * @covers PHP\BitTorrent\Decoder::decodeFile
     */
    public function testDecodeTorrentFileStrictWithMissingInfo() {
        $file = __DIR__ . '/_files/testMissingInfo.torrent';
        $this->decoder->decodeFile($file, true);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage File
     * @covers PHP\BitTorrent\Decoder::decodeFile
     */
    public function testDecodeNonReadableFile() {
        $file = __DIR__ . '/nonExistingFile';
        $this->decoder->decodeFile($file);
    }

    /**
     * @covers PHP\BitTorrent\Decoder::decodeFile
     */
    public function testDecodeFileWithStrictChecksEnabled() {
        $list = $this->decoder->decodeFile(__DIR__ . '/_files/valid.torrent', true);

        $this->assertInternalType('array', $list);
        $this->assertArrayHasKey('announce', $list);
        $this->assertSame('http://trackerurl', $list['announce']);
        $this->assertArrayHasKey('comment', $list);
        $this->assertSame('This is a comment', $list['comment']);
        $this->assertArrayHasKey('creation date', $list);
        $this->assertEquals(1323713688, $list['creation date']);
        $this->assertArrayHasKey('info', $list);
        $this->assertInternalType('array', $list['info']);
        $this->assertArrayHasKey('files', $list['info']);
        $this->assertSame(5, count($list['info']['files']));
        $this->assertArrayHasKey('name', $list['info']);
        $this->assertSame('PHP', $list['info']['name']);
    }
}
