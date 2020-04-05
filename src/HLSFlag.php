<?php

/**
 * This file is part of the PHP-FFmpeg-video-streaming package.
 *
 * (c) Amin Yazdanpanah <contact@aminyazdanpanah.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Streaming;


class HLSFlag
{
    /**
     *If this flag is set, the muxer will store all segments in a single MPEG-TS file, and will use
     * byte ranges in the playlist. HLS playlists generated with this way will have the version number 4
     */
    const SINGLE_FILE = "single_file";

    /**
     * Segment files removed from the playlist are deleted after a period of time equal to the duration
     * of the segment plus the duration of the playlist.
     */
    const DELETE_SEGMENTS = "delete_segments";

    /**
     * Append new segments into the end of old segment list, and remove the #EXT-X-ENDLIST from the old segment list.
     */
    const APPEND_LIST = "append_list";

    /**
     * Round the duration info in the playlist file segment info to integer values, instead of using floating point.
     */
    const ROUND_DURATIONS = "round_durations";

    /**
     * Add the #EXT-X-DISCONTINUITY tag to the playlist, before the first segment’s information.
     */
    const DISCONT_START = "discont_start";

    /**
     * Do not append the EXT-X-ENDLIST tag at the end of the playlist.
     */
    const OMIT_ENDLIST = "omit_endlist";

    /**
     * The file specified by hls_key_info_file will be checked periodically and detect updates to the encryption info.
     * Be sure to replace this file atomically, including the file containing the AES encryption key.
     */
    const PERIODIC_REKEY = "periodic_rekey";

    /**
     * Add the #EXT-X-INDEPENDENT-SEGMENTS to playlists that has video segments and when all the segments of that
     * playlist are guaranteed to start with a Key frame.
     */
    const INDEPENDENT_SEGMENTS = "independent_segments";

    /**
     * Add the #EXT-X-I-FRAMES-ONLY to playlists that has video segments and can play only I-frames in
     * the #EXT-X-BYTERANGE mode.
     */
    const IFRAMES_ONLY = "iframes_only";

    /**
     * Allow segments to start on frames other than keyframes. This improves behavior on some players when
     * the time between keyframes is inconsistent, but may make things worse on others, and can cause some
     * oddities during seeking. This flag should be used with the hls_time option.
     */
    const SPLIT_BY_TIME = "split_by_time";

    /**
     * Generate EXT-X-PROGRAM-DATE-TIME tags.
     */
    const PROGRAM_DATE_TIME = "program_date_time";

    /**
     * Makes it possible to use segment indexes as %%d in hls_segment_filename expression besides date/time values
     * when strftime is on. To get fixed width numbers with trailing zeroes, %%0xd format is available where x is
     * the required width.
     */
    const SECOND_LEVEL_SEGMENT_INDEX = "second_level_segment_index";

    /**
     * Makes it possible to use segment sizes (counted in bytes) as %%s in hls_segment_filename expression besides
     * date/time values when strftime is on. To get fixed width numbers with trailing zeroes, %%0xs format is available
     * where x is the required width.
     */
    const SECOND_LEVEL_SEGMENT_SIZE = "second_level_segment_size";

    /**
     * Makes it possible to use segment duration (calculated in microseconds) as %%t in hls_segment_filename
     * expression besides date/time values when strftime is on. To get fixed width numbers with trailing zeroes,
     * %%0xt format is available where x is the required width.
     */
    const SECOND_LEVEL_SEGMENT_DURATION = "second_level_segment_duration";

    /**
     * Write segment data to filename.tmp and rename to filename only once the segment is complete.
     * A webserver serving up segments can be configured to reject requests to *.tmp to prevent access to in-progress
     * segments before they have been added to the m3u8 playlist. This flag also affects how m3u8 playlist files are
     * created. If this flag is set, all playlist files will written into temporary file and renamed after they are
     * complete, similarly as segments are handled. But playlists with file protocol and with type (hls_playlist_type)
     * other than vod are always written into temporary file regardless of this flag. Master playlist files
     * (master_pl_name), if any, with file protocol, are always written into temporary file regardless of this flag
     * if master_pl_publish_rate value is other than zero.
     */
    const TEMP_FILE = "temp_file";
}