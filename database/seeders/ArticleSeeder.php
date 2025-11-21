<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        $tags = Tag::all();
        $author = User::first();

        if (!$author) {
            $author = User::create([
                'name' => 'Nguyễn Văn An',
                'email' => 'author@markettimes.vn',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
        }

        $articles = [
            // Tiêu điểm
            [
                'title' => 'Thủ tướng yêu cầu cắt bỏ những thủ tục không cần thiết mà người dân vẫn phải làm',
                'category' => 'tieu-diem',
                'summary' => 'Thủ tướng Chính phủ chỉ đạo các bộ, ngành khẩn trương rà soát, cắt giảm các thủ tục hành chính không cần thiết, gây phiền hà cho người dân và doanh nghiệp.',
                'is_featured' => true,
                'view_count' => 15420,
            ],
            [
                'title' => 'VN-Index tăng điểm mạnh mẽ trong phiên đầu tuần, vượt mốc 1.280 điểm',
                'category' => 'tieu-diem',
                'summary' => 'Thị trường chứng khoán Việt Nam ghi nhận phiên giao dịch tích cực với VN-Index tăng gần 20 điểm, thanh khoản đạt hơn 20.000 tỷ đồng.',
                'is_featured' => true,
                'view_count' => 12890,
            ],
            [
                'title' => 'Giá vàng trong nước vượt 81 triệu đồng/lượng, đạt đỉnh mới',
                'category' => 'tieu-diem',
                'summary' => 'Giá vàng miếng SJC tiếp tục tăng mạnh, vượt mốc 81 triệu đồng/lượng, cao hơn thế giới khoảng 8 triệu đồng.',
                'is_featured' => true,
                'view_count' => 11245,
            ],

            // Thẩm định giá
            [
                'title' => 'Vai trò của thẩm định giá trong hoạt động tín dụng ngân hàng',
                'category' => 'tham-dinh-gia',
                'summary' => 'Thẩm định giá tài sản đảm bảo là khâu quan trọng giúp ngân hàng đánh giá chính xác giá trị tài sản, giảm thiểu rủi ro tín dụng.',
                'view_count' => 3450,
            ],
            [
                'title' => 'Hội Thẩm định giá Việt Nam tổ chức Đại hội lần thứ VI',
                'category' => 'tham-dinh-gia',
                'summary' => 'Đại hội đã bầu Ban chấp hành nhiệm kỳ mới và đề ra định hướng phát triển nghề thẩm định giá trong giai đoạn 2024-2029.',
                'view_count' => 2890,
            ],
            [
                'title' => 'Thẩm định giá bất động sản: Những thách thức trong bối cảnh mới',
                'category' => 'tham-dinh-gia',
                'summary' => 'Thị trường bất động sản biến động đặt ra nhiều thách thức mới cho hoạt động thẩm định giá, đòi hỏi các tổ chức phải nâng cao năng lực chuyên môn.',
                'view_count' => 2345,
            ],
            [
                'title' => 'Chuẩn mực thẩm định giá quốc tế: Xu hướng áp dụng tại Việt Nam',
                'category' => 'tham-dinh-gia',
                'summary' => 'Việc áp dụng chuẩn mực thẩm định giá quốc tế (IVS) giúp nâng cao chất lượng, tính minh bạch của hoạt động thẩm định giá tại Việt Nam.',
                'view_count' => 1980,
            ],
            [
                'title' => 'Thẩm định giá doanh nghiệp: Phương pháp và ứng dụng thực tiễn',
                'category' => 'tham-dinh-gia',
                'summary' => 'Các phương pháp thẩm định giá doanh nghiệp phổ biến và ứng dụng trong M&A, tái cơ cấu doanh nghiệp và niêm yết cổ phiếu.',
                'view_count' => 1750,
            ],

            // Tài chính
            [
                'title' => 'NHNN giữ nguyên lãi suất điều hành, tiếp tục hỗ trợ tăng trưởng kinh tế',
                'category' => 'tai-chinh',
                'summary' => 'Ngân hàng Nhà nước quyết định giữ nguyên các mức lãi suất điều hành để hỗ trợ doanh nghiệp và nền kinh tế phục hồi.',
                'is_featured' => true,
                'view_count' => 8920,
            ],
            [
                'title' => 'Dòng tiền ngoại tiếp tục đổ vào thị trường chứng khoán Việt Nam',
                'category' => 'tai-chinh',
                'summary' => 'Nhà đầu tư nước ngoài mua ròng hơn 5.000 tỷ đồng trong tháng qua, cho thấy sức hấp dẫn của TTCK Việt Nam.',
                'view_count' => 7654,
            ],
            [
                'title' => 'Cổ phiếu ngân hàng bứt phá, dẫn dắt thị trường tăng điểm',
                'category' => 'tai-chinh',
                'summary' => 'Nhóm cổ phiếu ngân hàng như VCB, BID, CTG đồng loạt tăng giá, đóng góp lớn vào đà tăng của VN-Index.',
                'view_count' => 6890,
            ],
            [
                'title' => 'Lạm phát tháng 10 tăng 0,34%, CPI cả năm dự báo dưới 4%',
                'category' => 'tai-chinh',
                'summary' => 'Chỉ số giá tiêu dùng tháng 10 tăng nhẹ, lạm phát cả năm 2024 được dự báo ở mức 3,5-3,8%.',
                'view_count' => 5234,
            ],
            [
                'title' => 'Tín dụng 10 tháng tăng 9,2%, vượt chỉ tiêu đề ra',
                'category' => 'tai-chinh',
                'summary' => 'Tổng dư nợ tín dụng nền kinh tế tăng trưởng tích cực, đáp ứng nhu cầu vốn của doanh nghiệp và người dân.',
                'view_count' => 4567,
            ],
            [
                'title' => 'VIC, VHM dẫn dắt nhóm cổ phiếu vốn hóa lớn tăng điểm',
                'category' => 'tai-chinh',
                'summary' => 'Cổ phiếu Vingroup và Vinhomes ghi nhận mức tăng ấn tượng, kéo theo cả nhóm cổ phiếu vốn hóa lớn.',
                'view_count' => 4123,
            ],

            // Kinh doanh
            [
                'title' => 'Vingroup công bố kết quả kinh doanh quý 3: Lợi nhuận tăng trưởng 45%',
                'category' => 'kinh-doanh',
                'summary' => 'Tập đoàn Vingroup ghi nhận kết quả kinh doanh tích cực với doanh thu đạt 45.000 tỷ đồng, lợi nhuận sau thuế tăng 45% so với cùng kỳ.',
                'view_count' => 9876,
            ],
            [
                'title' => 'FDI 10 tháng đạt 27,5 tỷ USD, tăng 8,9% so với cùng kỳ',
                'category' => 'kinh-doanh',
                'summary' => 'Vốn đầu tư nước ngoài vào Việt Nam tiếp tục tăng trưởng tích cực, khẳng định sức hấp dẫn của môi trường đầu tư.',
                'view_count' => 7234,
            ],
            [
                'title' => 'Doanh nghiệp Việt Nam đẩy mạnh chuyển đổi số trong sản xuất',
                'category' => 'kinh-doanh',
                'summary' => 'Nhiều doanh nghiệp áp dụng công nghệ 4.0, tự động hóa quy trình sản xuất để nâng cao năng suất và chất lượng sản phẩm.',
                'view_count' => 6543,
            ],
            [
                'title' => 'Xuất khẩu nông sản Việt Nam đạt kỷ lục 55 tỷ USD',
                'category' => 'kinh-doanh',
                'summary' => 'Kim ngạch xuất khẩu nông sản tăng mạnh nhờ cà phê, gạo, rau quả và thủy sản có giá trị cao.',
                'view_count' => 5890,
            ],
            [
                'title' => 'Các doanh nghiệp startup Việt gọi vốn thành công hàng trăm triệu USD',
                'category' => 'kinh-doanh',
                'summary' => 'Hệ sinh thái startup Việt Nam tiếp tục phát triển với nhiều vòng gọi vốn thành công từ các quỹ đầu tư quốc tế.',
                'view_count' => 5234,
            ],
            [
                'title' => 'Nhịp cầu doanh nghiệp: Kết nối cung cầu trong chuỗi giá trị',
                'category' => 'kinh-doanh',
                'summary' => 'Chương trình Nhịp cầu doanh nghiệp giúp kết nối doanh nghiệp lớn với SMEs, tạo chuỗi cung ứng bền vững.',
                'view_count' => 4567,
            ],

            // Thế giới
            [
                'title' => 'Fed giữ nguyên lãi suất, thị trường toàn cầu hồi phục',
                'category' => 'the-gioi',
                'summary' => 'Cục Dự trữ Liên bang Mỹ (Fed) quyết định giữ nguyên lãi suất ở mức 5,25-5,5%, giúp thị trường tài chính toàn cầu ổn định.',
                'view_count' => 6234,
            ],
            [
                'title' => 'Kinh tế Trung Quốc tăng trưởng 5,3% trong quý 3',
                'category' => 'the-gioi',
                'summary' => 'Nền kinh tế lớn thứ hai thế giới ghi nhận mức tăng trưởng vượt kỳ vọng nhờ các biện pháp kích thích tiêu dùng.',
                'view_count' => 5678,
            ],
            [
                'title' => 'Giá dầu thế giới tăng lên mức 95 USD/thùng do căng thẳng Trung Đông',
                'category' => 'the-gioi',
                'summary' => 'Xung đột tại khu vực Trung Đông làm giá dầu Brent tăng mạnh, tác động đến nền kinh tế toàn cầu.',
                'view_count' => 4890,
            ],
            [
                'title' => 'EU thông qua gói hỗ trợ 50 tỷ Euro cho Ukraine',
                'category' => 'the-gioi',
                'summary' => 'Liên minh châu Âu đồng ý cung cấp gói viện trợ tài chính lớn để hỗ trợ Ukraine trong xung đột với Nga.',
                'view_count' => 3456,
            ],
            [
                'title' => 'Nhật Bản tăng lãi suất lần đầu tiên sau 17 năm',
                'category' => 'the-gioi',
                'summary' => 'Ngân hàng Trung ương Nhật Bản kết thúc chính sách lãi suất âm, đánh dấu bước ngoặt trong chính sách tiền tệ.',
                'view_count' => 2987,
            ],

            // Công nghệ
            [
                'title' => 'AI và Machine Learning: Xu hướng công nghệ dẫn đầu năm 2024',
                'category' => 'cong-nghe',
                'summary' => 'Trí tuệ nhân tạo và học máy tiếp tục là xu hướng công nghệ nóng nhất, được ứng dụng rộng rãi trong nhiều lĩnh vực.',
                'view_count' => 8765,
            ],
            [
                'title' => 'Việt Nam đẩy mạnh chuyển đổi số quốc gia đến 2025',
                'category' => 'cong-nghe',
                'summary' => 'Chính phủ ban hành nhiều chính sách ưu đãi, hỗ trợ doanh nghiệp và người dân tiếp cận công nghệ số.',
                'view_count' => 7234,
            ],
            [
                'title' => '5G tại Việt Nam: Cơ hội và thách thức cho doanh nghiệp viễn thông',
                'category' => 'cong-nghe',
                'summary' => 'Công nghệ 5G mở ra nhiều cơ hội mới nhưng đòi hỏi đầu tư lớn về hạ tầng và phổ tần số.',
                'view_count' => 6543,
            ],
            [
                'title' => 'Blockchain và ứng dụng trong tài chính, ngân hàng',
                'category' => 'cong-nghe',
                'summary' => 'Công nghệ blockchain được các ngân hàng và tổ chức tài chính nghiên cứu ứng dụng để tăng tính minh bạch và bảo mật.',
                'view_count' => 5432,
            ],
            [
                'title' => 'Startup fintech Việt Nam thu hút vốn đầu tư nước ngoài',
                'category' => 'cong-nghe',
                'summary' => 'Các startup fintech trong nước nhận được sự quan tâm lớn từ các quỹ đầu tư quốc tế nhờ mô hình kinh doanh sáng tạo.',
                'view_count' => 4876,
            ],
            [
                'title' => 'E-commerce Việt Nam tăng trưởng 25% trong năm 2024',
                'category' => 'cong-nghe',
                'summary' => 'Thương mại điện tử tiếp tục là điểm sáng với quy mô thị trường đạt 20 tỷ USD, tăng trưởng 25% so với năm trước.',
                'view_count' => 4321,
            ],

            // Bất động sản
            [
                'title' => 'Thị trường bất động sản Hà Nội hồi phục trong quý 3/2024',
                'category' => 'bat-dong-san',
                'summary' => 'Giao dịch bất động sản tại Hà Nội tăng 30% so với quý trước nhờ nguồn cung mới và chính sách hỗ trợ từ Chính phủ.',
                'view_count' => 9234,
            ],
            [
                'title' => 'TP.HCM điều chỉnh quy hoạch, mở rộng khu vực phát triển đô thị',
                'category' => 'bat-dong-san',
                'summary' => 'Thành phố thông qua quy hoạch mới, tạo điều kiện phát triển các khu đô thị hiện đại tại khu Đông và khu Nam.',
                'view_count' => 7654,
            ],
            [
                'title' => 'Bất động sản nghỉ dưỡng: Cơ hội đầu tư hấp dẫn năm 2024',
                'category' => 'bat-dong-san',
                'summary' => 'Thị trường bất động sản nghỉ dưỡng tại các tỉnh ven biển ghi nhận sự quan tâm lớn từ nhà đầu tư trong và ngoài nước.',
                'view_count' => 6543,
            ],
            [
                'title' => 'Giá đất nền tại các tỉnh phía Nam tăng 10-15%',
                'category' => 'bat-dong-san',
                'summary' => 'Nhu cầu mua đất nền để xây nhà ở tăng cao khiến giá đất tại nhiều tỉnh phía Nam tăng mạnh trong 6 tháng qua.',
                'view_count' => 5432,
            ],
            [
                'title' => 'Luật Đất đai mới: Những thay đổi quan trọng cho thị trường BĐS',
                'category' => 'bat-dong-san',
                'summary' => 'Luật Đất đai 2024 có nhiều điểm mới về thủ tục, giá đất, quyền sử dụng đất, tác động trực tiếp đến thị trường BĐS.',
                'view_count' => 4987,
            ],

            // Ngành hàng
            [
                'title' => 'Ngành thép Việt Nam đối mặt với áp lực cạnh tranh từ thép nhập khẩu',
                'category' => 'nganh-hang',
                'summary' => 'Giá thép trong nước giảm do lượng thép nhập khẩu tăng cao, các doanh nghiệp đang tìm cách tái cơ cấu để tồn tại.',
                'view_count' => 5678,
            ],
            [
                'title' => 'Ngành dệt may xuất khẩu đạt 38 tỷ USD trong 10 tháng',
                'category' => 'nganh-hang',
                'summary' => 'Kim ngạch xuất khẩu dệt may tăng 12% nhờ đơn hàng từ Mỹ, EU và các thị trường lớn tăng trở lại.',
                'view_count' => 4890,
            ],
            [
                'title' => 'Ngành ô tô Việt Nam: Triển vọng phát triển xe điện',
                'category' => 'nganh-hang',
                'summary' => 'VinFast và các hãng xe trong nước đẩy mạnh sản xuất xe điện, hướng đến mục tiêu trung hòa carbon.',
                'view_count' => 4234,
            ],
            [
                'title' => 'Ngành logistics Việt Nam hấp dẫn nhà đầu tư ngoại',
                'category' => 'nganh-hang',
                'summary' => 'Vị trí địa lý thuận lợi và cơ sở hạ tầng được cải thiện giúp ngành logistics thu hút vốn FDI lớn.',
                'view_count' => 3765,
            ],
            [
                'title' => 'Ngành thực phẩm đồ uống tăng trưởng 15% nhờ thay đổi thói quen tiêu dùng',
                'category' => 'nganh-hang',
                'summary' => 'Xu hướng tiêu dùng thực phẩm sạch, đồ uống có lợi cho sức khỏe thúc đẩy doanh thu ngành F&B tăng cao.',
                'view_count' => 3456,
            ],

            // More articles for better distribution
            [
                'title' => 'Đặc san Xuân Ất Tỵ 2025: Triển vọng kinh tế Việt Nam',
                'category' => 'tieu-diem',
                'summary' => 'Chuyên đề đặc biệt về triển vọng phát triển kinh tế Việt Nam trong năm 2025, với những dự báo và phân tích chuyên sâu.',
                'is_special_publication' => true,
                'view_count' => 2345,
            ],
            [
                'title' => 'Đặc san Thẩm định giá: 25 năm phát triển nghề nghiệp',
                'category' => 'tham-dinh-gia',
                'summary' => 'Xuất bản đặc biệt kỷ niệm 25 năm hình thành và phát triển nghề thẩm định giá tại Việt Nam.',
                'is_special_publication' => true,
                'view_count' => 1890,
            ],
            [
                'title' => 'Chính sách tiền tệ 2024: Cân bằng giữa tăng trưởng và lạm phát',
                'category' => 'tai-chinh',
                'summary' => 'NHNN điều hành chính sách tiền tệ linh hoạt, hỗ trợ tăng trưởng nhưng vẫn kiểm soát lạm phát ở mức thấp.',
                'view_count' => 3890,
            ],
            [
                'title' => 'Doanh nghiệp SME tiếp cận vốn vay từ ngân hàng: Còn nhiều khó khăn',
                'category' => 'kinh-doanh',
                'summary' => 'Doanh nghiệp vừa và nhỏ vẫn gặp khó trong việc tiếp cận nguồn vốn tín dụng từ ngân hàng do thiếu tài sản đảm bảo.',
                'view_count' => 2987,
            ],
            [
                'title' => 'Thương chiến Mỹ-Trung: Tác động đến chuỗi cung ứng toàn cầu',
                'category' => 'the-gioi',
                'summary' => 'Căng thẳng thương mại giữa hai nền kinh tế lớn nhất thế giới tiếp tục ảnh hưởng đến dòng chảy hàng hóa và đầu tư.',
                'view_count' => 3654,
            ],
            [
                'title' => 'Công nghệ xanh: Xu hướng đầu tư bền vững của doanh nghiệp',
                'category' => 'cong-nghe',
                'summary' => 'Ngày càng nhiều doanh nghiệp đầu tư vào công nghệ xanh, hướng đến mục tiêu phát triển bền vững.',
                'view_count' => 2876,
            ],
            [
                'title' => 'Dự án căn hộ cao cấp tại Thủ Thiêm thu hút khách hàng nước ngoài',
                'category' => 'bat-dong-san',
                'summary' => 'Khu đô thị mới Thủ Thiêm trở thành điểm đến hấp dẫn cho nhà đầu tư nước ngoài tìm kiếm bất động sản cao cấp.',
                'view_count' => 4123,
            ],
            [
                'title' => 'Ngành du lịch Việt Nam hồi phục mạnh, đón 120 triệu lượt khách',
                'category' => 'nganh-hang',
                'summary' => 'Du lịch nội địa và quốc tế đều tăng trưởng tích cực, góp phần quan trọng vào GDP quốc gia.',
                'view_count' => 5234,
            ],
        ];

        foreach ($articles as $articleData) {
            $category = $categories->where('slug', $articleData['category'])->first();

            if (!$category) {
                continue;
            }

            // Generate content
            $content = $this->generateContent($articleData['summary']);

            $article = Article::create([
                'title' => $articleData['title'],
                'slug' => Str::slug($articleData['title']),
                'summary' => $articleData['summary'],
                'content' => $content,
                'author_id' => $author->id,
                'category_id' => $category->id,
                'published_at' => now()->subDays(rand(1, 60)),
                'view_count' => $articleData['view_count'] ?? rand(100, 1000),
                'is_featured' => $articleData['is_featured'] ?? false,
                'is_special_publication' => $articleData['is_special_publication'] ?? false,
                'is_published' => true,
                'meta_title' => $articleData['title'],
                'meta_description' => $articleData['summary'],
            ]);

            // Attach random tags (2-5 tags per article)
            $randomTags = $tags->random(rand(2, 5));
            $article->tags()->attach($randomTags);
        }
    }

    private function generateContent($summary): string
    {
        $paragraphs = [
            '<p>' . $summary . '</p>',
            '<p>Theo các chuyên gia kinh tế, đây là dấu hiệu tích cực cho thấy nền kinh tế đang có những bước phục hồi rõ rệt sau giai đoạn khó khăn. Các chỉ số kinh tế vĩ mô đều có xu hướng cải thiện, tạo niềm tin cho nhà đầu tư và doanh nghiệp.</p>',
            '<p>Trong bối cảnh hiện nay, Chính phủ đã và đang triển khai nhiều giải pháp đồng bộ nhằm hỗ trợ doanh nghiệp, tháo gỡ khó khăn trong sản xuất kinh doanh. Các gói hỗ trợ tài chính, ưu đãi tín dụng, và cải cách thủ tục hành chính đã mang lại hiệu quả thiết thực.</p>',
            '<h2>Triển vọng trong thời gian tới</h2>',
            '<p>Các chuyên gia dự báo xu hướng tích cực này sẽ tiếp tục trong những tháng cuối năm. Tuy nhiên, vẫn còn nhiều thách thức cần được giải quyết để đảm bảo sự phát triển bền vững trong dài hạn.</p>',
            '<p>Theo số liệu thống kê, tốc độ tăng trưởng dự kiến sẽ đạt mức cao trong quý cuối năm nhờ vào sự phục hồi của cầu tiêu dùng nội địa và hoạt động xuất khẩu. Điều này sẽ góp phần đạt được mục tiêu tăng trưởng GDP đề ra cho cả năm.</p>',
            '<blockquote>Đây là thời điểm thuận lợi để các doanh nghiệp đầu tư mở rộng sản xuất kinh doanh, tận dụng các cơ hội từ thị trường trong nước và quốc tế.</blockquote>',
            '<h2>Khuyến nghị cho doanh nghiệp</h2>',
            '<p>Các doanh nghiệp cần chủ động nắm bắt xu hướng, đẩy mạnh đổi mới sáng tạo và ứng dụng công nghệ để nâng cao năng lực cạnh tranh. Việc liên kết, hợp tác giữa các doanh nghiệp cũng cần được tăng cường để cùng phát triển.</p>',
            '<p>Bên cạnh đó, việc tuân thủ các quy định pháp luật, đảm bảo trách nhiệm xã hội và bảo vệ môi trường cũng là yếu tố quan trọng để doanh nghiệp phát triển bền vững trong thời gian dài.</p>',
        ];

        return implode("\n", $paragraphs);
    }
}
